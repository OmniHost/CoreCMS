<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Administraton common header
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 *  @visibility private
 */
 

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
        $this->data['analytics'] = $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['captcha'] = $this->url->link('extension/captcha', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['theme'] = $this->url->link('extension/theme', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['modifications'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_online'] = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['report_user_activity'] = $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['currency'] = $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['backup'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['language'] = $this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['api_user'] = $this->url->link('user/api', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['marketing'] = $this->url->link('marketing/marketing', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['zone'] = $this->url->link('localisation/zone', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['geo_zone'] = $this->url->link('localisation/geo_zone', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['tax_class'] = $this->url->link('localisation/tax_class', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['order_status'] = $this->url->link('localisation/order_status', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['tax_rate'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'], 'SSL');
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

        $this->data['menu_theme'] = $this->language->get('menu_theme');
        $this->data['menu_analytics'] = $this->language->get('menu_analytics');
        $this->data['menu_captcha'] = $this->language->get('menu_captcha');

        $this->data['menu_currency'] = $this->language->get('menu_currency');
        $this->data['menu_backup'] = $this->language->get('menu_backup');
        $this->data['menu_language'] = $this->language->get('menu_language');

        $this->data['menu_zone'] = $this->language->get('menu_zone');
        $this->data['menu_geo_zone'] = $this->language->get('menu_geo_zone');
        $this->data['menu_tax_rate'] = $this->language->get('menu_tax_rate');
        $this->data['menu_tax_class'] = $this->language->get('menu_tax_class');
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


        $this->data['header_navs'] = array();

        if ($this->user->hasPermission('access', 'dashboard/calendar')) {
            $this->load->model('dashboard/calendar');


            $this->data['header_navs']['calendar'] = array(
                'title' => $this->language->get('alert_calendar'),
                'icon' => 'fa fa-calendar',
                'class' => 'warning',
                'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                'order' => 0,
                'total' => $this->model_dashboard_calendar->getTodayCount()
            );
        }
        $this->data['header_navs']['alerts'] = array(
            'title' => $this->language->get('alert_user'),
            'icon' => 'fa fa-bell-o',
            'class' => 'warning',
            'total' => $alerts['customers']['total'] + $alerts['comments']['total'] + $alerts['contacts']['total'],
            'items' => $alerts,
            'order' => 1
        );

        if ($this->config->get('config_maintenance')) {
            $this->data['header_navs']['alerts']['items']['maintenance'] = array(
                'href' => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL'),
                'text' => $this->language->get('Change Maintenance Settings'),
                'class' => 'danger',
                'total' => 1
            );
            $this->data['header_navs']['alerts']['total'] ++;
            $this->data['header_navs']['alerts']['class'] = 'danger';
        }

        if ($this->data['header_navs']['alerts']['total'] < 1) {
            $this->data['header_navs']['alerts']['class'] = 'success';
        }


        $this->event->trigger('admin.header.navs', $this->data['header_navs']);


        sort_this_array($this->data['header_navs'], 'order', SORT_ASC);

        $this->data['module_menu'] = array(
            'dashboard' => array(
                'order' => 0,
                'icon' => 'fa-dashboard',
                'label' => $this->language->get('menu_dashboard'),
                'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                'children' => array()
            ),
            'cms' => array(
                'order' => 1,
                'icon' => 'fa-file-text-o',
                'label' => $this->language->get('menu_content'),
                'href' => '#',
                'children' => array(
                    'pages' => array(
                        'label' => $this->data['menu_pages'],
                        'href' => $this->data['pages'],
                        'order' => 0,
                        'route' => 'cms/page',
                    ),
                    'posts' => array(
                        'label' => $this->data['menu_posts'],
                        'href' => $this->data['posts'],
                        'order' => 1,
                        'route' => 'blog/post',
                    ),
                    'blog_category' => array(
                        'label' => $this->data['menu_blog_category'],
                        'href' => $this->data['blog_category'],
                        'order' => 2,
                        'route' => 'blog/category',
                    ),
                    'tags' => array(
                        'label' => $this->language->get("Post Tags"),
                        'href' => $this->url->link('blog/tags', 'token=' . $this->session->data['token'], 'SSL'),
                        'order' => 3,
                        'route' => 'blog/tags',
                    ),
                    'banner' => array(
                        'label' => $this->data['menu_banners'],
                        'href' => $this->data['banner'],
                        'order' => 5,
                        'route' => 'cms/banner',
                    ),
                    'comments' => array(
                        'label' => $this->data['menu_comments'],
                        'href' => $this->data['comments'],
                        'order' => 4,
                        'route' => 'cms/comment',
                    ),
                    'download' => array(
                        'label' => $this->data['menu_download'],
                        'href' => $this->data['download'],
                        'order' => 6,
                        'route' => 'cms/download',
                    ),
                    'menu' => array(
                        'label' => $this->data['menu_menus'],
                        'href' => $this->data['menu'],
                        'order' => 7,
                        'route' => 'design/menu',
                    )
                )
            ),
            'logout' => array(
                'order' => 99,
                'icon' => 'fa-lock',
                'label' => $this->data['menu_logout'],
                'href' => $this->data['logout'],
                'children' => array()
            ),
            'communication' => array(
                'order' => 2,
                'icon' => 'fa-envelope-o',
                'label' => $this->language->get('menu_communication'),
                'badge' => $this->data['total_contact_us'] ? '<span class="badge bg-red pull-right">' . $this->data['total_contact_us'] . '</span>' : '',
                'href' => '#',
                'children' => array(
                    'contact_form' => array(
                        'label' => $this->language->get('menu_contact_us'),
                        'href' => $this->data['contact_us'],
                        'order' => 0,
                        'badge' => $this->data['total_contact_us'] ? '<span class="badge bg-red pull-right">' . $this->data['total_contact_us'] . '</span>' : '',
                        'route' => 'sale/contact',
                    ),
                    'contact_email' => array(
                        'label' => $this->language->get('Email Users'),
                        'href' => $this->url->link('marketing/contact', 'token=' . $this->session->data['token'], 'SSL'),
                        'order' => 1,
                        'route' => 'marketing/contact',
                    ),
                    'marketing' => array(
                        'label' => $this->data['menu_marketing'],
                        'href' => $this->data['marketing'],
                        'order' => 2,
                        'children' => array(),
                        'route' => 'marketing/marketing',
                    ),
                    'forms' => array(
                        'label' => $this->data['menu_formbuilder'],
                        'href' => $this->data['formbuilder'],
                        'order' => 3,
                        'children' => array(),
                        'route' => 'marketing/form',
                    ),
                    'newsletters' => array(
                        'label' => $this->language->get('menu_newsletter_system'),
                        'href' => '#',
                        'order' => 4,
                        'route' => 'marketing/newsletter',
                        'children' => array(
                            'overview' => array(
                                'label' => $this->language->get('menu_newsletter_overview'),
                                'href' => $this->data['newsletter_overview'],
                                'order' => 0,
                            ),
                            'subscribers' => array(
                                'label' => $this->language->get('menu_subscriber'),
                                'href' => $this->data['subscriber'],
                                'order' => 1,
                            ),
                            'groups' => array(
                                'label' => $this->language->get('menu_newsletter_group'),
                                'href' => $this->data['newsletter_groups'],
                                'order' => 2,
                            ),
                            'campaigns' => array(
                                'label' => $this->language->get('menu_newsletter_campaign'),
                                'href' => $this->data['newsletter_campaigns'],
                                'order' => 3,
                            ),
                            'newsletters' => array(
                                'label' => $this->language->get('menu_newsletter_newsletter'),
                                'href' => $this->data['newsletter_newsletter'],
                                'order' => 4,
                            ),
                        )
                    )
                )
            ),
            'system' => array(
                'order' => 10,
                'icon' => 'fa-cogs',
                'label' => $this->language->get('menu_system'),
                'href' => '#',
                'children' => array(
                    'setting' => array(
                        'label' => $this->language->get('menu_setting'),
                        'href' => $this->data['setting'],
                        'order' => 0,
                        'route' => 'setting/setting',
                    ),
                    'layout' => array('label' => $this->language->get('menu_layout'),
                        'href' => $this->data['layout'],
                        'order' => 1,
                        'route' => 'design/layout',
                    ),
                    'custom_field' => array('label' => $this->language->get('menu_custom_field'),
                        'href' => $this->data['custom_field'],
                        'order' => 3,
                        'route' => 'sale/custom_field',
                    ),
                    'upload' => array('label' => $this->language->get('menu_upload'),
                        'href' => $this->data['upload'],
                        'order' => 4,
                        'route' => 'tool/upload',
                    ),
                    'backup' => array('label' => $this->language->get('menu_backup'),
                        'href' => $this->data['backup'],
                        'order' => 5,
                        'route' => 'tool/backup',
                    ),
                    'localisation' => array('label' => $this->language->get('menu_localisation'),
                        'href' => '#',
                        'order' => 2,
                        'children' => array(
                            'country' => array('label' => $this->language->get('menu_country'),
                                'href' => $this->data['country'],
                                'order' => 0,
                                'route' => 'localisation/country',
                            ),
                            'zone' => array('label' => $this->language->get('menu_zone'),
                                'href' => $this->data['zone'],
                                'order' => 1,
                                'route' => 'localisation/zone',
                            ),
                            'geo_zone' => array('label' => $this->language->get('menu_geo_zone'),
                                'href' => $this->data['geo_zone'],
                                'order' => 2,
                                'route' => 'localisation/geo_zone',
                            ),
                            'tax_class' => array('label' => $this->language->get('menu_tax_class'),
                                'href' => $this->data['tax_class'],
                                'order' => 4,
                                'route' => 'localisation/tax_class',
                            ),
                            'tax_rate' => array('label' => $this->language->get('menu_tax_rate'),
                                'href' => $this->data['tax_rate'],
                                'order' => 3,
                                'route' => 'localisation/tax_rate',
                            ),
                            'currency' => array('label' => $this->language->get('menu_currency'),
                                'href' => $this->data['currency'],
                                'order' => 5,
                                'route' => 'localisation/currency',
                            ),
                            'currency' => array('label' => $this->language->get('menu_order_status'),
                                'href' => $this->data['order_status'],
                                'order' => 6,
                                'route' => 'localisation/order_status',
                            ),
                            'language' => array('label' => $this->language->get('menu_language'),
                                'href' => $this->data['language'],
                                'order' => 99,
                                'route' => 'localisation/language',
                            ),
                        )
                    )
                )
            ),
            'users' => array(
                'order' => 3,
                'icon' => 'fa-user',
                'label' => $this->language->get('menu_user'),
                'href' => '#',
                'children' => array(
                    'user_admin' => array(
                        'label' => $this->language->get('menu_user_admin'),
                        'href' => '#',
                        'order' => 0,
                        'children' => array(
                            'user' => array('label' => $this->language->get('menu_user'),
                                'href' => $this->data['user'],
                                'order' => 0,
                                'route' => 'user/user',
                            ),
                            'user_group' => array('label' => $this->language->get('menu_user_group'),
                                'href' => $this->data['user_group'],
                                'order' => 1,
                                'route' => 'user/user_permission',
                            ),
                        )
                    ),
                    'user_public' => array(
                        'label' => $this->language->get('menu_user_public'),
                        'href' => '#',
                        'order' => 1,
                        'children' => array(
                            'user' => array('label' => $this->language->get('menu_user'),
                                'href' => $this->data['user_front'],
                                'order' => 0,
                                'route' => 'sale/customer',
                            ),
                            'user_group' => array('label' => $this->language->get('menu_user_group'),
                                'href' => $this->data['user_group_front'],
                                'order' => 1,
                                'route' => 'sale/customer_group',
                            ),
                            'user_ban' => array('label' => $this->language->get('menu_user_ip_ban'),
                                'href' => $this->data['user_group_ban'],
                                'order' => 2,
                                'route' => 'sale/customer_ban_ip',
                            )
                        )
                    ),
                    'user_api' => array('label' => $this->language->get('menu_api_user'),
                        'href' => $this->data['api_user'],
                        'order' => 2,
                        'route' => 'user/api',
                    ),
                )
            ),
            'reports' => array(
                'order' => 4,
                'icon' => 'fa-bar-chart',
                'label' => $this->language->get('menu_reports'),
                'href' => '#',
                'children' => array(
                    'user_activity' => array('label' => $this->language->get('menu_report_user_activity'),
                        'href' => $this->data['report_user_activity'],
                        'order' => 0,
                        'route' => 'report/customer_activity',
                    ),
                    'user_online' => array('label' => $this->language->get('menu_report_user_online'),
                        'href' => $this->data['report_user_online'],
                        'order' => 1,
                        'route' => 'report/customer_online',
                    ),
                )
            ),
            'extensions' => array(
                'order' => 5,
                'icon' => 'fa-puzzle-piece',
                'label' => $this->language->get('menu_extension'),
                'href' => '#',
                'children' => array(
                    'modules' => array('label' => $this->language->get('menu_modules'),
                        'href' => $this->data['modules'],
                        'order' => 2,
                        'route' => 'extension/module',
                    ),
                    'modifications' => array('label' => $this->language->get('menu_modifications'),
                        'href' => $this->data['modifications'],
                        'order' => 1,
                        'route' => 'extension/modification',
                    ),
                    'installer' => array('label' => $this->language->get('menu_install'),
                        'href' => $this->data['installer'],
                        'order' => 0,
                        'route' => 'extension/installer',
                    ),
                     'theme' => array('label' => $this->language->get('menu_theme'),
                        'href' => $this->data['theme'],
                        'order' => 8,
                        'route' => 'extension/theme',
                    ),
                     'analytics' => array('label' => $this->language->get('menu_analytics'),
                        'href' => $this->data['analytics'],
                        'order' => 3,
                        'route' => 'extension/analytics',
                    ),
                     'captcha' => array('label' => $this->language->get('menu_captcha'),
                        'href' => $this->data['captcha'],
                        'order' => 4,
                        'route' => 'extension/captcha',
                    ),
                    'seo_urls' => array('label' => $this->language->get('SEO Urls'),
                        'href' => $this->data['seo_urls'],
                        'order' => 7,
                        'route' => 'tool/seourl',
                    ),
                    'payments' => array('label' => $this->language->get('Payment'),
                        'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
                        'order' => 6,
                        'route' => 'extension/payment',
                    ),
                    'feeds' => array('label' => $this->language->get('menu_feeds'),
                        'href' => $this->data['feeds'],
                        'order' => 5,
                        'route' => 'extension/feed',
                    ),
                )
            )
               
        );
        $this->event->trigger('admin.module.menu', $this->data['module_menu']);


        //sort our Menu!!
        //first lets sort by the top key:::
        sort_this_array($this->data['module_menu'], 'order', SORT_ASC);
        foreach ($this->data['module_menu'] as &$menu_item) {
            if (!empty($menu_item['children'])) {
                sort_this_array($menu_item['children'], 'order', SORT_ASC);
            }
        }

        $this->template = 'common/header.phtml';
        $this->render();
    }

}
