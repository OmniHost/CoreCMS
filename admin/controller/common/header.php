<?php

class ControllerCommonHeader extends \Core\Controller {

    public function index() {
        $this->language->load('common/header');
        $this->data['title'] = $this->document->getTitle();

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        $this->data['text_logout'] = $this->language->get('text_logout');
        $this->data['logout'] = $this->url->link('user/logout', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['home'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['user'] = $this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['user_group'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['user_front'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['user_group_front'] = $this->url->link('sale/customer_group', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['user_group_ban'] = $this->url->link('sale/customer_ban_ip', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['pages'] = $this->url->link('cms/page', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['posts'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['banner'] = $this->url->link('cms/banner', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['feeds'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['download'] = $this->url->link('cms/download', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['country'] = $this->url->link('localisation/country', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['blog_category'] = $this->url->link('blog/category', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['layout'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['menu'] = $this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['modules'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['setting'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['installer'] = $this->url->link('extension/installer', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['modifications'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_online'] = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_activity'] = $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['currency'] = $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['backup'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['language'] = $this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['api_user'] = $this->url->link('user/api', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['marketing'] = $this->url->link('marketing/marketing', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['zone'] = $this->url->link('localisation/zone', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['custom_field'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['upload'] = $this->url->link('tool/upload', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['contact_us'] = $this->url->link('sale/contact', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['newsletter_overview'] = $this->url->link('marketing/newsletter/overview', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['newsletter_groups'] = $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['newsletter_campaigns'] = $this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['newsletter_newsletter'] = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['subscriber'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['newsletter'] = $this->url->link('marketing/contact', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['seo_urls'] = $this->url->link('tool/seourl', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['formbuilder'] = $this->url->link('marketing/form', 'token=' . $this->session->data['token'], 'SSL');


        $this->data['menu_dashboard'] = $this->language->get('menu_dashboard');
        $this->data['menu_logout'] = $this->language->get('menu_logout');
        $this->data['menu_user'] = $this->language->get('menu_user');
        $this->data['menu_user_admin'] = $this->language->get('menu_user_admin');
        $this->data['menu_user_public'] = $this->language->get('menu_user_public');
        $this->data['menu_user_ip_ban'] = $this->language->get('menu_user_ip_ban');
        $this->data['menu_modifications'] = $this->language->get('menu_modifications');
        $this->data['menu_user_group'] = $this->language->get('menu_user_group');
        $this->data['menu_system'] = $this->language->get('menu_system');
        $this->data['menu_setting'] = $this->language->get('menu_setting');
        $this->data['menu_layout'] = $this->language->get('menu_layout');
        $this->data['menu_extension'] = $this->language->get('menu_extension');
        $this->data['menu_install'] = $this->language->get('menu_install');
        $this->data['menu_modules'] = $this->language->get('menu_modules');
        $this->data['menu_content'] = $this->language->get('menu_content');
        $this->data['menu_pages'] = $this->language->get('menu_pages');
        $this->data['menu_banners'] = $this->language->get('menu_banners');
        $this->data['menu_feeds'] = $this->language->get('menu_feeds');
        $this->data['menu_country'] = $this->language->get('menu_country');
        $this->data['menu_api_user'] = $this->language->get('menu_api_user');
        $this->data['menu_localisation'] = $this->language->get('menu_localisation');

        $this->data['menu_download'] = $this->language->get('menu_download');
        $this->data['menu_menus'] = $this->language->get('menu_menus');
        $this->data['menu_posts'] = $this->language->get('menu_posts');
        $this->data['menu_blog_category'] = $this->language->get('menu_blog_category');
        $this->data['menu_reports'] = $this->language->get('menu_reports');
        $this->data['menu_report_user_activity'] = $this->language->get('menu_report_user_activity');
        $this->data['menu_report_user_online'] = $this->language->get('menu_report_user_online');
        $this->data['menu_marketing'] = $this->language->get('menu_marketing');
        $this->data['menu_formbuilder'] = $this->language->get('Form Builder');


        $this->data['menu_currency'] = $this->language->get('menu_currency');
        $this->data['menu_backup'] = $this->language->get('menu_backup');
        $this->data['menu_language'] = $this->language->get('menu_language');

        $this->data['menu_zone'] = $this->language->get('menu_zone');
        $this->data['menu_upload'] = $this->language->get('menu_upload');
        $this->data['menu_custom_field'] = $this->language->get('menu_custom_field');





        $this->data['text_customer'] = $this->language->get('text_customer');
        $this->data['text_online'] = $this->language->get('text_online');
        $this->data['text_approval'] = $this->language->get('text_approval');
        $this->data['text_review'] = $this->language->get('text_review');


        $this->data['links'] = $this->document->getLinks();

        $this->load->model('user/user');
        $user = $this->model_user_user->getUser($this->user->getId());
        $this->data['member_since'] = DATE("M Y", strtotime($user['date_added']));
        $this->data['member_profile'] = $this->url->link('user/user/update', 'user_id=' . $this->user->getId() . '&token=' . $this->session->data['token'], 'SSL');


        $this->load->model('tool/image');
        $profile_img = $this->model_tool_image->resizeExact($user['profile_img'], 100, 100);
        if (!$profile_img) {
            $profile_img = $this->model_tool_image->resizeExact('avatar_generic.jpg', 100, 100);
        }
        $this->data['profile_img'] = $profile_img;



        $this->load->model('report/customer');
        $alerts = array();
        $countonline = $this->model_report_customer->getTotalCustomersOnline();
        $alerts['online'] = array(
            'href' => $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_user_online'),
            'class' => $countonline ? 'success' : 'warning',
            'total' => $countonline);

        $this->load->model('sale/customer');
        $toapprove = $this->model_sale_customer->getTotalCustomers(array('filter_approved' => false));
        $alerts['customers'] = array(
            'href' => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&filter_approved=0', 'SSL'),
            'text' => $this->language->get('text_user_approve'),
            'class' => ($toapprove) ? 'danger' : 'success',
            'total' => $toapprove
        );

        $this->load->model('cms/comment');
        $tomoderate = $this->model_cms_comment->getTotalComments(array('filter_status' => false));
        $alerts['comments'] = array(
            'href' => $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&filter_status=0', 'SSL'),
            'text' => $this->language->get('text_comment'),
            'class' => ($tomoderate) ? 'danger' : 'success',
            'total' => $tomoderate
        );

        $query = $this->db->query("select count(*) as total from #__contact where is_read = 0");
        $contacts = $this->data['total_contact_us'] = $query->row['total'];
        $alerts['contacts'] = array(
            'href' => $this->url->link('sale/contact', 'token=' . $this->session->data['token'] . '&filter_status=0', 'SSL'),
            'text' => $this->language->get('text_contacts'),
            'class' => ($contacts) ? 'danger' : 'success',
            'total' => $contacts
        );


        $this->data['menu_comments'] = $this->language->get('menu_comments');
        $this->data['comments'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['total_comments'] = $alerts['comments']['total'];



        $this->data['header_navs'] = array(
            'alerts' => array(
                'title' => $this->language->get('alert_user'),
                'icon' => 'fa fa-bell-o',
                'class' => 'warning',
                'total' => $alerts['customers']['total'] + $alerts['comments']['total'] + $alerts['contacts']['total'],
                'items' => $alerts
            )
        );

        if ($this->config->get('config_maintenance')) {
            $this->data['header_navs']['alerts']['items']['maintenance'] = array(
                    'href' => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL'),
                    'text' => $this->language->get('Change Maintenance Settings'),
                    'class' => 'danger',
                'total' => 1
                );
            $this->data['header_navs']['alerts']['total']++;
            $this->data['header_navs']['alerts']['class'] = 'danger';
        }
        
        if($this->data['header_navs']['alerts']['total'] < 1){
             $this->data['header_navs']['alerts']['class'] = 'success';
        }


        $this->event->trigger('admin.header.navs', $this->data['header_navs']);


        $this->data['module_menu'] = array();
        $this->event->trigger('admin.module.menu', $this->data['module_menu']);


        $this->template = 'common/header.phtml';
        $this->render();
    }

}
