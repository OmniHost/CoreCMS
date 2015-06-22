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
        $this->data['blog_category'] = $this->url->link('blog/category', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['layout'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['menu'] = $this->url->link('design/menu', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['modules'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['setting'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['installer'] = $this->url->link('extension/installer', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['modifications'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_online'] = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_activity'] = $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'], 'SSL');

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
        $this->data['menu_menus'] = $this->language->get('menu_menus');
        $this->data['menu_posts'] = $this->language->get('menu_posts');
        $this->data['menu_blog_category'] = $this->language->get('menu_blog_category');
        $this->data['menu_reports'] = $this->language->get('menu_reports');
        $this->data['menu_report_user_activity'] = $this->language->get('menu_report_user_activity');
        $this->data['menu_report_user_online'] = $this->language->get('menu_report_user_online');




        $data['text_customer'] = $this->language->get('text_customer');
        $data['text_online'] = $this->language->get('text_online');
        $data['text_approval'] = $this->language->get('text_approval');
        $data['text_review'] = $this->language->get('text_review');


        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->document->resetStyles();

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
        $alerts['online'] = array(
            'href' => $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_user_online'),
            'class' => 'success',
            'total' => $this->model_report_customer->getTotalCustomersOnline());

        $this->load->model('sale/customer');
        $alerts['customers'] = array(
            'href' => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&filter_approved=0', 'SSL'),
            'text' => $this->language->get('text_user_approve'),
            'class' => 'danger',
            'total' => $this->model_sale_customer->getTotalCustomers(array('filter_approved' => false))
        );

        $this->load->model('cms/comment');
        $alerts['comments'] = array(
            'href' => $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&filter_status=0', 'SSL'),
            'text' => $this->language->get('text_comment'),
            'class' => 'danger',
            'total' => $this->model_cms_comment->getTotalComments(array('filter_status' => false))
        );


        $this->data['menu_comments'] = $this->language->get('menu_comments');
        $this->data['comments'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['total_comments'] = $alerts['comments']['total'];



        $data['header_navs'] = array(
            'alerts' => array(
                'title' => $this->language->get('alert_user'),
                'icon' => 'fa fa-bell-o',
                'class' => 'warning',
                'total' => $alerts['customers']['total'] + $alerts['comments']['total'],
                'items' => $alerts
            )
        );



        $this->data['header_navs'] = \Core\HookPoints::executeHooks('admin_header_navs', $data['header_navs']);


        $this->template = 'common/header.phtml';
        $this->render();
    }

}
