<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Communication - Form Builder
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingForm extends \Core\Controller {

    private $error = array();

    public function index() {


        $this->load->language('marketing/form');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/form');



        $this->getList();
    }

    public function insert() {

        $this->load->language('marketing/form');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/form');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $form_id = $this->model_marketing_form->addForm($_POST);
            if ($form_id == -1) {
                echo $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL');
                die;
            }


            echo $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL');

            die;
        }

        $this->getForm();
    }

    public function update() {
        $this->load->model('setting/setting');

        $this->load->language('marketing/form');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/form');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_marketing_form->editForm($this->request->get['form_id'], $this->request->post);

            echo $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL');

            die;
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->model('setting/setting');
        $this->load->language('marketing/form');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/form');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $creator_id) {
                $result = $this->model_setting_setting->getSetting('creator');
                $this->model_marketing_form->deleteform($creator_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }
    
    public function genSeoUrl(){
        $page_name = slug(trim(html_entity_decode($this->request->get['name'])));
        $this->load->model('tool/seo');
        return $this->model_tool_seo->getUniqueSlug($page_name, $page_id);
    }

    private function getList() {

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['insert'] = $this->url->link('marketing/form/insert', 'token=' . $this->session->data['token'], 'SSL');
        $data['delete'] = $this->url->link('marketing/form/delete', 'token=' . $this->session->data['token'], 'SSL');

        $data['categories'] = array();



        $results = $this->model_marketing_form->getForms(0);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'class' => 'primary',
                'icon' => 'fa fa-edit',
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('marketing/form/update', 'token=' . $this->session->data['token'] . '&form_id=' . $result['form_id'], 'SSL')
            );

            $data['categories'][] = array(
                'form_id' => $result['form_id'],
                'title' => $result['title'],
                'status' => $result['status'],
                'selected' => isset($this->request->post['selected']) && in_array($result['form_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_delete'] = $this->language->get('button_delete');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');
        $this->response->setOutput($this->render('marketing/form_list.phtml', $data));
    }

    private function getForm() {
        $this->load->model('setting/setting');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_none'] = $this->language->get('text_none');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_percent'] = $this->language->get('text_percent');
        $data['text_amount'] = $this->language->get('text_amount');
        $data['text_content_top'] = $this->language->get('text_content_top');
        $data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $data['text_column_left'] = $this->language->get('text_column_left');
        $data['text_column_right'] = $this->language->get('text_column_right');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_keyword'] = $this->language->get('entry_keyword');
        $data['entry_parent'] = $this->language->get('entry_parent');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_top'] = $this->language->get('entry_top');
        $data['entry_column'] = $this->language->get('entry_column');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_layout'] = $this->language->get('entry_layout');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data'] = $this->language->get('tab_data');
        $data['tab_design'] = $this->language->get('tab_design');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['creator_id'])) {
            $data['action'] = $this->url->link('marketing/form/insert', 'token=' . $this->session->data['token'], 'SSL');
            $data['text_edit'] = $this->language->get('text_add');
        } else {
            $data['action'] = $this->url->link('marketing/form/update', 'token=' . $this->session->data['token'] . '&creator_id=' . $this->request->get['creator_id'], 'SSL');
            $data['text_edit'] = $this->language->get('text_edit');
        }

        $data['cancel'] = $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL');

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['form_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $creator_info = $this->model_marketing_form->getForm($this->request->get['form_id']);
        }


        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($creator_info)) {
            $data['title'] = $creator_info['title'];
        } else {
            $data['title'] = '';
        }

        if (isset($this->request->post['url'])) {
            $data['url'] = $this->request->post['url'];
        } elseif (!empty($creator_info)) {
            $data['url'] = $creator_info['url'];
        } else {
            $data['url'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($creator_info)) {
            $data['email'] = $creator_info['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['success_msg'])) {
            $data['success_msg'] = $this->request->post['success_msg'];
        } elseif (!empty($creator_info)) {
            $data['success_msg'] = $creator_info['success_msg'];
        } else {
            $data['success_msg'] = '';
        }


        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($creator_info)) {
            $data['status'] = $creator_info['status'];
        } else {
            $data['status'] = 1;
        }

        $data['html'] = isset($creator_info) ? $this->model_marketing_form->getFormEdit(unserialize($creator_info['formdata'])) : '';

        
        $this->document->addStyle("view/template/marketing/externals/css/custom.css");
        $this->document->addScript('view/plugins/jQueryUI/jquery-ui.js');

        $this->document->addScript("view/template/marketing/externals/js/drag-drop-custom.js");
        $this->document->addScript("view/template/marketing/externals/js/jquery.blockUI.js");

        
        

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');
        $this->response->setOutput($this->render('marketing/form_form.phtml', $data));
    }
    
  

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'marketing/form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }


        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'marketing/form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
