<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Modules - Latest blog posts
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerModuleBlogLatest extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/blog_latest');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('blog_latest', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_post_count'] = $this->language->get('entry_post_count');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['post_count'])) {
            $data['error_post_count'] = $this->error['post_count'];
        } else {
            $data['error_post_count'] = '';
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

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/blog_latest', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('module/blog_latest', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['blog_latest_status'])) {
            $data['blog_latest_status'] = $this->request->post['blog_latest_status'];
        } else {
            $data['blog_latest_status'] = $this->config->get('blog_latest_status');
        }

        if (isset($this->request->post['blog_latest_post_count'])) {
            $data['blog_latest_post_count'] = $this->request->post['blog_latest_post_count'];
        } else {
            $data['blog_latest_post_count'] = $this->config->get('blog_latest_post_count');
        }

        if (isset($this->request->post['blog_latest_title'])) {
            $data['blog_latest_title'] = $this->request->post['blog_latest_title'];
        } else {
            $data['blog_latest_title'] = $this->config->get('blog_latest_title');
        }

        if ($data['blog_latest_post_count'] <= 0) {
            $data['blog_latest_post_count'] = 5;
        }

        $this->data = $data;
        $this->template = 'module/blog_latest.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/blog_latest')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((int) $this->request->post['blog_latest_post_count'] < 1) {
            $this->error['post_count'] = $this->language->get('error_post_count');
        }

        return !$this->error;
    }

}
