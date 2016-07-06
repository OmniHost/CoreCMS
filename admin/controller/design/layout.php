<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name System - Layout Manager
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
class ControllerDesignLayout extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('design/layout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/layout');

        $this->getList();
    }

    public function insert() {
        $this->language->load('design/layout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/layout');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_design_layout->addLayout($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function update() {
        $this->language->load('design/layout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/layout');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_design_layout->editLayout($this->request->get['layout_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->language->load('design/layout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/layout');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $layout_id) {
                $this->model_design_layout->deleteLayout($layout_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('design/layout/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('design/layout/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['layouts'] = array();

        $data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $layout_total = $this->model_design_layout->getTotalLayouts();

        $results = $this->model_design_layout->getLayouts($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('design/layout/update', 'token=' . $this->session->data['token'] . '&layout_id=' . $result['layout_id'] . $url, 'SSL')
            );

            $this->data['layouts'][] = array(
                'layout_id' => $result['layout_id'],
                'name' => $result['name'],
                'selected' => isset($this->request->post['selected']) && in_array($result['layout_id'], $this->request->post['selected']),
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

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $layout_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->template = 'design/layout_list.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm() {

        $this->data['text_form'] = !isset($this->request->get['layout_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $this->data['text_default'] = $this->language->get('text_default');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');

        $this->data['text_column_top'] = $this->language->get('text_column_top');
        $this->data['text_column_bottom'] = $this->language->get('text_column_bottom');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $this->data['text_column_left'] = $this->language->get('text_column_left');
        $this->data['text_column_right'] = $this->language->get('text_column_right');

        $this->data['entry_name'] = $this->language->get('entry_name');

        $this->data['entry_route'] = $this->language->get('entry_route');
        $this->data['entry_module'] = $this->language->get('entry_module');
        $this->data['entry_position'] = $this->language->get('entry_position');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_route'] = $this->language->get('button_add_route');
        $this->data['button_module_add'] = $this->language->get('button_module_add');
        $this->data['button_remove'] = $this->language->get('button_remove');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_default'] = $this->language->get('text_default');


        
         $this->data['templates'] = array();
        $directories = glob(DIR_ROOT . 'view/template/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }


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

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['layout_id'])) {
            $this->data['action'] = $this->url->link('design/layout/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $this->data['action'] = $this->url->link('design/layout/update', 'token=' . $this->session->data['token'] . '&layout_id=' . $this->request->get['layout_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['layout_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $layout_info = $this->model_design_layout->getLayout($this->request->get['layout_id']);
        }

        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } elseif (!empty($layout_info)) {
            $this->data['name'] = $layout_info['name'];
        } else {
            $this->data['name'] = '';
        }
        
        if (isset($this->request->post['override'])) {
            $this->data['override'] = $this->request->post['override'];
        } elseif (!empty($layout_info)) {
            $this->data['override'] = ($layout_info['template']) ? $layout_info['template']:$this->config->get('config_template');
        } else {
            $this->data['override'] = $this->config->get('config_template');
        }

        if (isset($this->request->post['layout_route'])) {
            $this->data['layout_routes'] = $this->request->post['layout_route'];
        } elseif (isset($this->request->get['layout_id'])) {
            $this->data['layout_routes'] = $this->model_design_layout->getLayoutRoutes($this->request->get['layout_id']);
        } else {
            $this->data['layout_routes'] = array();
        }

        if (isset($this->request->post['layout_module'])) {
            $this->data['layout_modules'] = $this->request->post['layout_module'];
        } elseif (isset($this->request->get['layout_id'])) {
            $this->data['layout_modules'] = $this->model_design_layout->getLayoutModules($this->request->get['layout_id']);
        } else {
            $this->data['layout_modules'] = array();
        }


        $this->load->model('extension/extension');
        $this->load->model('extension/module');

        $this->data['extensions'] = array();

        $extensions = $this->model_extension_extension->getInstalled('module');

        // Add all the modules which have multiple settings for each module
        foreach ($extensions as $code) {
            $this->load->language('module/' . $code);

            $module_data = array();

            $modules = $this->model_extension_module->getModulesByCode($code);

            foreach ($modules as $module) {
                $module_data[] = array(
                    'name' => $this->language->get('heading_title') . ' &gt; ' . $module['name'],
                    'code' => $code . '.' . $module['module_id']
                );
            }

            if ($this->config->has($code . '_status') || $module_data) {
                $this->data['extensions'][] = array(
                    'name' => $this->language->get('heading_title'),
                    'code' => $code,
                    'module' => $module_data
                );
            }
        }

        $this->data['template_positions'] = array();

        $templates = array('default' => json_decode(file_get_contents(DIR_ROOT . 'view/template/default/config.json')));
        //$template = $this->config->get('config_template');
        $template = $this->data['override'];

        $template_configs = array();
        if (is_file(DIR_ROOT . 'view/template/' . $template . '/config.json')) {
            $template_configs = json_decode(file_get_contents(DIR_ROOT . 'view/template/' . $template . '/config.json'), 1);
        }
        if ($template_configs && !empty($template_configs['extra_positions'])) {
            $this->data['template_positions'] = $template_configs['extra_positions'];
        }
        

        $this->template = 'design/layout_form.phtml';
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
        
        if(!empty($this->request->post['refresh'])){
            return false;
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


        foreach ($this->request->post['selected'] as $layout_id) {

            if ($this->config->get('config_layout_id') == $layout_id) {
                $this->error['warning'] = $this->language->get('error_default');
            }


            $page_total = $this->db->query("select count(*) as total from #__ams_pages where layout_id='" . (int) $layout_id . "' and status='1'")->row['total'];
            if ($page_total) {
                $this->error['warning'] = sprintf($this->language->get('error_information'), $page_total);
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
