<?php

class ControllerDesignMenu extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('design/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/menu');

        $this->getList();
    }

    public function delete() {
        $this->language->load('design/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/menu');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $menu_id) {
                $this->model_design_menu->deleteMenu($menu_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');


            $this->redirect($this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('design/menu/add', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['delete'] = $this->url->link('design/menu/edit', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['menus'] = array();



        $results = $this->model_design_menu->getMenus();

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('design/menu/edit', 'token=' . $this->session->data['token'] . '&menu_id=' . $result['menu_id'], 'SSL')
            );

            $this->data['menus'][] = array(
                'menu_id' => $result['menu_id'],
                'name' => $result['name'],
                'items' => $this->model_design_menu->countMenuItems($result['menu_id']),
                'header' => $result['header'] ? $this->language->get('text_heading') : '',
                'footer' => $result['footer'] ? $this->language->get('text_footer') : '',
                'selected' => isset($this->request->post['selected']) && in_array($result['menu_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }




        $this->template = 'design/menu_list.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function add() {
        $this->language->load('design/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/menu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $post = $this->request->post;
            $post['items'] = json_decode(html_entity_decode($post['items']),1);
            $this->model_design_menu->addMenu($post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->language->load('design/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/menu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
             $post = $this->request->post;
            $post['items'] = json_decode(html_entity_decode($post['items']),1);
      
            $this->model_design_menu->editMenu($this->request->get['menu_id'], $post);

            $this->session->data['success'] = $this->language->get('text_success');


            $this->redirect($this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function menuitem() {
        $this->template = 'design/menu_item.phtml';
        $this->response->setOutput($this->render());
    }

    protected function getForm() {

        $this->data['text_form'] = !isset($this->request->get['menu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');


        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_header'] = $this->language->get('entry_header');
        $this->data['entry_footer'] = $this->language->get('entry_footer');
        $this->data['entry_items'] = $this->language->get('entry_items');
        $this->data['entry_structure'] = $this->language->get('entry_structure');
        $this->data['entry_allowed_groups'] = $this->language->get('entry_allowed_groups');
        $this->data['entry_denied_groups'] = $this->language->get('entry_denied_groups');
        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_link'] = $this->language->get('entry_link');
        $this->data['help_link'] = $this->language->get('help_link');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_item'] = $this->language->get('button_add_item');
        $this->data['button_remove'] = $this->language->get('button_remove');

        $this->data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['catalog_uri'] = HTTPS_CATALOG;
        } else {
            $this->data['catalog_uri'] = HTTP_CATALOG;
        }


        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['menu_id'])) {
            $this->data['action'] = $this->url->link('design/menu/add', 'token=' . $this->session->data['token'], 'SSL');
            $this->data['menu_id'] = false;
        } else {
            $this->data['action'] = $this->url->link('design/menu/edit', 'token=' . $this->session->data['token'] . '&menu_id=' . $this->request->get['menu_id'], 'SSL');
            $this->data['menu_id'] = $this->request->get['menu_id'];
        }

        $this->data['cancel'] = $this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $menu_info = $this->model_design_menu->getMenu($this->request->get['menu_id']);
        }

        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } elseif (!empty($menu_info)) {
            $this->data['name'] = $menu_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($this->request->post['header'])) {
            $this->data['isheader'] = $this->request->post['header'];
        } elseif (!empty($menu_info)) {
            $this->data['isheader'] = $menu_info['header'];
        } else {
            $this->data['isheader'] = 0;
        }

        if (isset($this->request->post['footer'])) {
            $this->data['isfooter'] = $this->request->post['footer'];
        } elseif (!empty($menu_info)) {
            $this->data['isfooter'] = $menu_info['footer'];
        } else {
            $this->data['isfooter'] = 0;
        }


        if (isset($this->request->post['items'])) {
            $items = json_decode(html_entity_decode($this->request->post['items']),1);
        } elseif (!empty($menu_info)) {
            $items = $menu_info['items'];
        } else {
            $items = array();
        }
        
        
        $this->data['items'] = $items;
   

        $this->data['token'] = $this->session->data['token'];


        $this->load->model('sale/customer_group');
        $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(array('sort' => 'cg.sort_order'));


        $this->document->addScript("view/plugins/nestable/nestable.js");
        $this->document->addStyle("view/plugins/nestable/nestable.css");

        $this->template = 'design/menu_form.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'design/layout')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'design/layout')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

       

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
