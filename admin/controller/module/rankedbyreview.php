<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Modules - Ranked By Review system
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
class ControllerModuleRankedByReview extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/rankedbyreview');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_extension_module->addModule('rankedbyreview', $this->request->post);
            } else {
                $this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_business_id'] = $this->language->get('entry_business_id');
        $data['entry_widget'] = $this->language->get('entry_widget');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_title'] = $this->language->get('entry_title');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['business_id'])) {
            $data['error_business_id'] = $this->error['business_id'];
        } else {
            $data['error_business_id'] = '';
        }
        
        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/rankedbyreview', 'token=' . $this->session->data['token'], 'SSL')
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/rankedbyreview', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('module/rankedbyreview', 'token=' . $this->session->data['token'], 'SSL');
        } else {
            $data['action'] = $this->url->link('module/rankedbyreview', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
        }

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['business_id'])) {
            $data['business_id'] = $this->request->post['business_id'];
        } elseif (!empty($module_info)) {
            $data['business_id'] = $module_info['business_id'];
        } else {
            $data['business_id'] = '';
        }

        if (isset($this->request->post['widget'])) {
            $data['widget'] = $this->request->post['widget'];
        } elseif (!empty($module_info)) {
            $data['widget'] = $module_info['widget'];
        } else {
            $data['widget'] = 'review';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '0';
        }


        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['title'] = $module_info['title'];
        } else {
            $data['title'] = '';
        }



        $data['token'] = $this->session->data['token'];

        /*  $data['header'] = $this->load->controller('common/header');
          $data['column_left'] = $this->load->controller('common/column_left');
          $data['footer'] = $this->load->controller('common/footer');

          $this->response->setOutput($this->load->view('module/menu.tpl', $data)); */
        $this->data = $data;
        $this->template = 'module/rankedbyreview.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );


        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/rankedbyreview')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        if ((int) $this->request->post['business_id'] < 1) {
            $this->error['business_id'] = $this->language->get('error_business_id');
        }

        return !$this->error;
    }

}
