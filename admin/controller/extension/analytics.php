<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Extendsions - Analytics
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 * 
 */
class ControllerExtensionAnalytics extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/analytics');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/extension');

        $this->getList();
    }

    public function install() {
        $this->load->language('extension/analytics');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/extension');

        if ($this->validate()) {
            $this->model_extension_extension->install('analytics', $this->request->get['extension']);

            $this->load->model('user/user_group');

            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'analytics/' . $this->request->get['extension']);
            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'analytics/' . $this->request->get['extension']);

            // Call install method if it exsits
            $this->load->controller('analytics/' . $this->request->get['extension'] . '/install');

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true));
        }

        $this->getList();
    }

    public function uninstall() {
        $this->load->language('extension/analytics');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/extension');

        if ($this->validate()) {
            $this->model_extension_extension->uninstall('analytics', $this->request->get['extension']);

            // Call uninstall method if it exsits
            $this->load->controller('analytics/' . $this->request->get['extension'] . '/uninstall');

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true));
        }
    }

    public function getList() {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_install'] = $this->language->get('button_install');
        $data['button_uninstall'] = $this->language->get('button_uninstall');

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

        $extensions = $this->model_extension_extension->getInstalled('analytics');

        foreach ($extensions as $key => $value) {
            if (!file_exists(DIR_APPLICATION . 'controller/analytics/' . $value . '.php')) {
                $this->model_extension_extension->uninstall('analytics', $value);

                unset($extensions[$key]);
            }
        }

        $this->load->model('setting/setting');


        $data['extensions'] = array();

        $files = glob(DIR_APPLICATION . 'controller/analytics/*.php');

        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('analytics/' . $extension);


                $data['extensions'][] = array(
                    'name' => $this->language->get('heading_title'),
                    'install' => $this->url->link('extension/analytics/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
                    'uninstall' => $this->url->link('extension/analytics/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
                    'installed' => in_array($extension, $extensions),
                    'edit' => $this->url->link('analytics/' . $extension, 'token=' . $this->session->data['token'], true),
                    'status' => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
                );
            }
        }

        $data['header'] = $this->getChild('common/header');

        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->load->view('extension/analytics', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/analytics')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}