<?php

class ControllerSettingSetting extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            if ($this->config->get('config_currency_auto')) {
				$this->load->model('localisation/currency');

				$this->model_localisation_currency->refresh();
			}
            
            $this->model_setting_setting->editSetting('config', $this->request->post);

            \Core\HookPoints::executeHooks('admin_settings_save');
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect(fixajaxurl($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['tab_general'] = $this->language->get('tab_general');

        $data['tab_server'] = $this->language->get('tab_server');
        $data['tab_google'] = $this->language->get('tab_google');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_template'] = $this->language->get('entry_template');
        $data['text_mail'] = $this->language->get('text_mail');
        $data['text_smtp'] = $this->language->get('text_smtp');
        $data['text_google_analytics'] = $this->language->get('text_google_analytics');
        $data['text_google_captcha'] = $this->language->get('text_google_captcha');
        $data['entry_limit_admin'] = $this->language->get('entry_limit_admin');
        $data['entry_logo'] = $this->language->get('entry_logo');
        $data['entry_icon'] = $this->language->get('entry_icon');
        $data['entry_mail_protocol'] = $this->language->get('entry_mail_protocol');
        $data['entry_mail_parameter'] = $this->language->get('entry_mail_parameter');
        $data['entry_mail_smtp_hostname'] = $this->language->get('entry_mail_smtp_hostname');
        $data['entry_mail_smtp_username'] = $this->language->get('entry_mail_smtp_username');
        $data['entry_mail_smtp_password'] = $this->language->get('entry_mail_smtp_password');
        $data['entry_mail_smtp_port'] = $this->language->get('entry_mail_smtp_port');
        $data['entry_mail_smtp_timeout'] = $this->language->get('entry_mail_smtp_timeout');
        $data['entry_mail_alert'] = $this->language->get('entry_mail_alert');
        $data['entry_maintenance'] = $this->language->get('entry_maintenance');
        $data['entry_secure'] = $this->language->get('entry_secure');
        $data['entry_encryption'] = $this->language->get('entry_encryption');
        $data['entry_compression'] = $this->language->get('entry_compression');
        $data['entry_error_display'] = $this->language->get('entry_error_display');
        $data['entry_error_log'] = $this->language->get('entry_error_log');
        $data['entry_google_analytics'] = $this->language->get('entry_google_analytics');
        $data['entry_google_captcha_public'] = $this->language->get('entry_google_captcha_public');
        $data['entry_google_captcha_secret'] = $this->language->get('entry_google_captcha_secret');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['help_limit_admin'] = $this->language->get('help_limit_admin');
        $data['help_icon'] = $this->language->get('help_icon');
        $data['help_mail_protocol'] = $this->language->get('help_mail_protocol');
        $data['help_mail_parameter'] = $this->language->get('help_mail_parameter');
        $data['help_mail_smtp_hostname'] = $this->language->get('help_mail_smtp_hostname');
        $data['help_mail_alert'] = $this->language->get('help_mail_alert');
        $data['help_secure'] = $this->language->get('help_secure');
        $data['entry_currency'] = $this->language->get('entry_currency');
        $data['help_currency'] = $this->language->get('help_currency');
        $data['entry_currency_auto'] = $this->language->get('entry_currency_auto'); 
        $data['help_currency_auto'] = $this->language->get('help_currency_auto');
        $data['entry_robots'] = $this->language->get('entry_robots');

        $data['help_maintenance'] = $this->language->get('help_maintenance');
        $data['help_encryption'] = $this->language->get('help_encryption');
        $data['help_compression'] = $this->language->get('help_compression');
        $data['help_google_analytics'] = $this->language->get('help_google_analytics');
        $data['help_google_captcha'] = $this->language->get('help_google_captcha');

        $data['entry_ftp_hostname'] = $this->language->get('entry_ftp_hostname');
        $data['entry_ftp_port'] = $this->language->get('entry_ftp_port');
        $data['entry_ftp_username'] = $this->language->get('entry_ftp_username');
        $data['entry_ftp_password'] = $this->language->get('entry_ftp_password');
        $data['entry_ftp_root'] = $this->language->get('entry_ftp_root');
        $data['entry_ftp_status'] = $this->language->get('entry_ftp_status');
        $data['help_ftp_root'] = $this->language->get('help_ftp_root');
        $data['tab_ftp'] = $this->language->get('tab_ftp');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_width'] = $this->language->get('entry_width');
        $data['entry_height'] = $this->language->get('entry_height');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = '';
        }

        if (isset($this->error['limit_admin'])) {
            $data['error_limit_admin'] = $this->error['limit_admin'];
        } else {
            $data['error_limit_admin'] = '';
        }

        if (isset($this->error['encryption'])) {
            $data['error_encryption'] = $this->error['encryption'];
        } else {
            $data['error_encryption'] = '';
        }


        if (isset($this->error['ftp_hostname'])) {
            $data['error_ftp_hostname'] = $this->error['ftp_hostname'];
        } else {
            $data['error_ftp_hostname'] = '';
        }

        if (isset($this->error['ftp_port'])) {
            $data['error_ftp_port'] = $this->error['ftp_port'];
        } else {
            $data['error_ftp_port'] = '';
        }

        if (isset($this->error['ftp_username'])) {
            $data['error_ftp_username'] = $this->error['ftp_username'];
        } else {
            $data['error_ftp_username'] = '';
        }

        if (isset($this->error['ftp_password'])) {
            $data['error_ftp_password'] = $this->error['ftp_password'];
        } else {
            $data['error_ftp_password'] = '';
        }

        if(isset($this->error['product_limit'])){
            $data['error_product_limit'] = $this->error['product_limit'];
        }else{
            $data['error_product_limit'] = '';
        }
        
        if(isset($this->error['blog_limit'])){
            $data['error_blog_limit'] = $this->error['blog_limit'];
        }else{
            $data['error_blog_limit'] = '';
        }
        
        if(isset($this->error['image_thumb'])){
            $data['error_image_thumb'] = $this->error['image_thumb'];
        }else{
            $data['error_image_thumb'] = '';
        }
        
         if(isset($this->error['image_popup'])){
            $data['error_image_popup'] = $this->error['image_popup'];
        }else{
            $data['error_image_popup'] = '';
        }
        
         if(isset($this->error['image_blogcat'])){
            $data['error_image_blogcat'] = $this->error['image_blogcat'];
        }else{
            $data['error_image_blogcat'] = '';
        }
        
        if(isset($this->error['customer_group_display'])){
            $data['error_customer_group_display'] = $this->error['customer_group_display'];
        }else{
            $data['error_customer_group_display'] = '';
        }
        
        if(isset($this->error['login_attempts'])){
            $data['error_login_attempts'] = $this->error['login_attempts'];
        }else{
            $data['error_login_attempts'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );



        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['action'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');


        $data['token'] = $this->session->data['token'];

        foreach ($this->config->keys() as $cfgk) {
            if (isset($this->request->post[$cfgk])) {
                $data[$cfgk] = $this->request->post[$cfgk];
            } else {
                $data[$cfgk] = $this->config->get($cfgk);
            }
        }
        $this->load->model('design/layout');
        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['templates'] = array();
        $directories = glob(DIR_ROOT . 'view/template/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $data['templates'][] = basename($directory);
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['config_logo']) && is_file(DIR_IMAGE . $this->request->post['config_logo'])) {
            $data['logo'] = $this->model_tool_image->resize($this->request->post['config_logo'], 100, 100);
        } elseif ($this->config->get('config_logo') && is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
        } else {
            $data['logo'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        if (isset($this->request->post['config_icon']) && is_file(DIR_IMAGE . $this->request->post['config_icon'])) {
            $data['icon'] = $this->model_tool_image->resize($this->request->post['config_logo'], 100, 100);
        } elseif ($this->config->get('config_icon') && is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $data['icon'] = $this->model_tool_image->resize($this->config->get('config_icon'), 100, 100);
        } else {
            $data['icon'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        if (isset($this->request->post['config_country_id'])) {
            $data['config_country_id'] = $this->request->post['config_country_id'];
        } else {
            $data['config_country_id'] = $this->config->get('config_country_id');
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->post['config_currency'])) {
            $data['config_currency'] = $this->request->post['config_currency'];
        } else {
            $data['config_currency'] = $this->config->get('config_currency');
        }
        $this->load->model('localisation/currency');

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        
        if (isset($this->request->post['config_currency_auto'])) {
			$data['config_currency_auto'] = $this->request->post['config_currency_auto'];
		} else {
			$data['config_currency_auto'] = $this->config->get('config_currency_auto');
		}


        if (isset($this->request->post['config_mail_smtp_port'])) {
            $data['config_mail_smtp_port'] = $this->request->post['config_mail_smtp_port'];
        } elseif ($this->config->has('config_mail_smtp_port')) {
            $data['config_mail_smtp_port'] = $this->config->get('config_mail_smtp_port');
        } else {
            $data['config_mail_smtp_port'] = 25;
        }

        if (isset($this->request->post['config_mail_smtp_timeout'])) {
            $data['config_mail_smtp_timeout'] = $this->request->post['config_mail_smtp_timeout'];
        } elseif ($this->config->has('config_mail_smtp_timeout')) {
            $data['config_mail_smtp_timeout'] = $this->config->get('config_mail_smtp_timeout');
        } else {
            $data['config_mail_smtp_timeout'] = 5;
        }

        if (isset($this->request->post['config_ftp_hostname'])) {
            $data['config_ftp_hostname'] = $this->request->post['config_ftp_hostname'];
        } elseif ($this->config->get('config_ftp_hostname')) {
            $data['config_ftp_hostname'] = $this->config->get('config_ftp_hostname');
        } else {
            $data['config_ftp_hostname'] = str_replace('www.', '', $this->request->server['HTTP_HOST']);
        }

        if (isset($this->request->post['config_ftp_port'])) {
            $data['config_ftp_port'] = $this->request->post['config_ftp_port'];
        } elseif ($this->config->get('config_ftp_port')) {
            $data['config_ftp_port'] = $this->config->get('config_ftp_port');
        } else {
            $data['config_ftp_port'] = 21;
        }


        $this->load->model('sale/customer_group');

        $filter_data = array(
            'filter_system' => 1
        );

        $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups($filter_data);
        if (isset($this->request->post['config_customer_group_display'])) {
            $data['config_customer_group_display'] = $this->request->post['config_customer_group_display'];
        } elseif ($this->config->get('config_customer_group_display')) {
            $data['config_customer_group_display'] = $this->config->get('config_customer_group_display');
        } else {
            $data['config_customer_group_display'] = array();
        }

        if (isset($this->request->post['config_login_attempts'])) {
            $data['config_login_attempts'] = $this->request->post['config_login_attempts'];
        } elseif ($this->config->has('config_login_attempts')) {
            $data['config_login_attempts'] = $this->config->get('config_login_attempts');
        } else {
            $data['config_login_attempts'] = 5;
        }

        $this->load->model('cms/page');
        $data['informations'] = $this->model_cms_page->getPages()->rows;


        $data['tabs'] = array();

        $data['placeholder'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        $data['tabs'] = \Core\HookPoints::executeHooks('admin_settings_settings', $data['tabs']);

        ksort($data['tabs']);
        $this->data = $data;

        $this->template = 'setting/setting.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'setting/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['config_name']) {
            $this->error['name'] = $this->language->get('error_name');
        }


        if ((utf8_strlen($this->request->post['config_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['config_email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }


        if (!$this->request->post['config_meta_title']) {
            $this->error['meta_title'] = $this->language->get('error_meta_title');
        }

        if (!$this->request->post['config_limit_admin']) {
            $this->error['limit_admin'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['config_product_limit']) {
            $this->error['config_product_limit'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['config_blog_limit']) {
            $this->error['config_blog_limit'] = $this->language->get('error_limit');
        }

        if ((utf8_strlen($this->request->post['config_encryption']) < 3) || (utf8_strlen($this->request->post['config_encryption']) > 32)) {
            $this->error['encryption'] = $this->language->get('error_encryption');
        }

        if ($this->request->post['config_ftp_status']) {
            if (!$this->request->post['config_ftp_hostname']) {
                $this->error['ftp_hostname'] = $this->language->get('error_ftp_hostname');
            }

            if (!$this->request->post['config_ftp_port']) {
                $this->error['ftp_port'] = $this->language->get('error_ftp_port');
            }

            if (!$this->request->post['config_ftp_username']) {
                $this->error['ftp_username'] = $this->language->get('error_ftp_username');
            }

            if (!$this->request->post['config_ftp_password']) {
                $this->error['ftp_password'] = $this->language->get('error_ftp_password');
            }
        }

        $this->error = \Core\HookPoints::executeHooks('admin_settings_validate', $this->error);

        if ($this->error && !isset($this->error['warning'])) {

            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function template() {
      
         if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }
        
        if (is_file(DIR_ROOT . 'view/template/' . basename($this->request->get['template']) . '/template.png')) {
            $this->response->setOutput($server . 'view/template/' . basename($this->request->get['template']) . '/template.png');
        } else {
            $this->response->setOutput($server . 'img/no_image.jpg');
        }
    }

}
