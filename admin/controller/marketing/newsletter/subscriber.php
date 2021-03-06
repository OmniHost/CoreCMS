<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Newsletter subscribers
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingNewsletterSubscriber extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter/subscriber');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/subscriber');

        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/newsletter/subscriber');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/subscriber');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_subscriber->addSubscriber($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_firstname'])) {
                $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_lastname'])) {
                $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
            }


            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/newsletter/subscriber');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/subscriber');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_subscriber->editSubscriber($this->request->get['subscriber_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_firstname'])) {
                $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_lastname'])) {
                $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('marketing/newsletter/subscriber');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/subscriber');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $subscriber_id) {
                $this->model_marketing_newsletter_subscriber->deleteSubscriber($subscriber_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_firstname'])) {
                $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_lastname'])) {
                $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }

        if (isset($this->request->get['filter_firstname'])) {
            $filter_firstname = $this->request->get['filter_firstname'];
        } else {
            $filter_firstname = null;
        }

        if (isset($this->request->get['filter_lastname'])) {
            $filter_lastname = $this->request->get['filter_lastname'];
        } else {
            $filter_lastname = null;
        }


        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'date_created';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_firstname'])) {
            $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_lastname'])) {
            $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
        }


        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/newsletter/subscriber/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('marketing/newsletter/subscriber/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['subscribers'] = array();

        $filter_data = array(
            'filter_email' => $filter_email,
            'filter_date_added' => $filter_date_added,
            'filter_firstname' => $filter_firstname,
            'filter_lastname' => $filter_lastname,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $subscriber_total = $this->model_marketing_newsletter_subscriber->getTotalSubscribers($filter_data);

        $results = $this->model_marketing_newsletter_subscriber->getSubscribers($filter_data);

        foreach ($results as $result) {
            $result['opt_in'] = $result['unsubscribe_date'] ? $this->language->get('text_yes') : $this->language->get('text_no');
            $result['date_added'] = date($this->language->get('date_format_short'), strtotime($result['date_created']));
            $result['edit'] = $this->url->link('marketing/newsletter/subscriber/edit', 'token=' . $this->session->data['token'] . '&subscriber_id=' . $result['subscriber_id'] . $url, 'SSL');

            $result['sent'] = 0;
            $result['opened'] = 0;
            $result['bounced'] = 0;

            $data['subscribers'][] = $result;
        }

        $data['heading_title'] = $this->language->get('heading_title');


        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_groups'] = $this->language->get('column_groups');
        $data['column_campaigns'] = $this->language->get('column_campaigns');
        $data['column_sent'] = $this->language->get('column_sent');
        $data['column_opened'] = $this->language->get('column_opened');
        $data['column_bounced'] = $this->language->get('column_bounced');
        $data['column_firstname'] = $this->language->get('column_firstname');
        $data['column_lastname'] = $this->language->get('column_lastname');
        $data['column_email'] = $this->language->get('column_email');
        $data['column_ip'] = $this->language->get('column_ip');
        $data['column_opt_in'] = $this->language->get('column_opt_in');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_date_created'] = $this->language->get('entry_date_created');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_firstname'])) {
            $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_lastname'])) {
            $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_firstname'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . '&sort=firstname' . $url, 'SSL');
        $data['sort_lastname'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . '&sort=lastname' . $url, 'SSL');
        $data['sort_email'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . '&sort=email' . $url, 'SSL');
        $data['sort_ip'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . '&sort=ip_address' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . '&sort=date_created' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_firstname'])) {
            $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_lastname'])) {
            $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->text = $this->language->get('text_pagination');
        $pagination->total = $subscriber_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();


        $data['filter_email'] = $filter_email;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_firstname'] = $filter_firstname;
        $data['filter_lastname'] = $filter_lastname;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');


        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');

        $this->response->setOutput($this->render('marketing/newsletter/subscriber_list.phtml', $data));
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['subscriber_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_opt_in'] = $this->language->get('entry_opt_in');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
        }


        $url = '';

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_firstname'])) {
            $url .= '&filter_firstname=' . urlencode(html_entity_decode($this->request->get['filter_firstname'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_lastname'])) {
            $url .= '&filter_lastname=' . urlencode(html_entity_decode($this->request->get['filter_lastname'], ENT_QUOTES, 'UTF-8'));
        }


        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['subscriber_id'])) {
            $data['action'] = $this->url->link('marketing/newsletter/subscriber/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/newsletter/subscriber/edit', 'token=' . $this->session->data['token'] . '&subscriber_id=' . $this->request->get['subscriber_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/newsletter/subscriber', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['subscriber_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $subscriber_info = $this->model_marketing_newsletter_subscriber->getSubscriber($this->request->get['subscriber_id']);
        }

        $data['token'] = $this->session->data['token'];


        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($subscriber_info)) {
            $data['email'] = $subscriber_info['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } elseif (!empty($subscriber_info)) {
            $data['firstname'] = $subscriber_info['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } elseif (!empty($subscriber_info)) {
            $data['lastname'] = $subscriber_info['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->request->post['group_id'])) {
            $group_id = $this->request->post['group_id'];
        } elseif (!empty($subscriber_info)) {
            $group_id = $subscriber_info['group_id'];
        } else {
            $group_id = array();
        }
        $data['group_id'] = array();
        $this->load->model('marketing/newsletter/group');
        if($group_id){
            foreach($group_id as $gid){
                $group = $this->model_marketing_newsletter_group->getGroup($gid);
               if($group){
                $data['group_id'][] = array(
                    'id' => $group['group_id'],
                    'name' => $group['group_name']
                );
               }
            }
        }

        if (isset($this->request->post['campaign_id'])) {
            $campaign_id = $this->request->post['campaign_id'];
        } elseif (!empty($subscriber_info)) {
            $campaign_id = $subscriber_info['campaign_id'];
        } else {
            $campaign_id = array();
        }

        $data['campaign_id'] = array();
        $this->load->model('marketing/newsletter/campaign');
         if($campaign_id){
            foreach($campaign_id as $gid){
                $camp = $this->model_marketing_newsletter_campaign->getCampaign($gid);
               if($group){
                $data['campaign_id'][] = array(
                    'id' => $camp['campaign_id'],
                    'name' => $camp['campaign_name']
                );
               }
            }
        }
        
        $data['entry_groups'] = $this->language->get('entry_groups');
        $data['entry_campaigns'] = $this->language->get('entry_campaigns');

        $data['groups_field'] = array(
            'key' => 'group_id',
            'type' => 'autocomplete_list',
            'value' => $data['group_id'],
            'label' => $this->language->get('entry_groups'),
            'url' => $this->url->link('marketing/newsletter/group/autocomplete', 'token=' . $this->session->data['token'], 'SSL'),
            'required' => false
        );


        $data['campaigns_field'] = array(
            'key' => 'campaign_id',
            'type' => 'autocomplete_list',
            'value' => $data['campaign_id'],
            'label' => $this->language->get('entry_campaigns'),
            'url' => $this->url->link('marketing/newsletter/campaign/autocomplete', 'token=' . $this->session->data['token'], 'SSL'),
            'required' => false
        );


        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/subscriber_form.phtml', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        } else {
            $this->load->model('marketing/newsletter/subscriber');
            $subid = isset($this->request->get['subscriber_id']) ? $this->request->get['subscriber_id'] : false;
            if ($this->model_marketing_newsletter_subscriber->hasEntry($this->request->post['email'], $subid)) {
                $this->error['email'] = $this->language->get('error_email_exists');
            }
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}
