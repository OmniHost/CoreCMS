<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Newsletter Campaigns
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingNewsletterCampaign extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter/campaign');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('marketing/newsletter/campaign');
        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/newsletter/campaign');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/campaign');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_campaign->addCampaign($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';


            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/newsletter/campaign');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/campaign');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_campaign->editCampaign($this->request->get['campaign_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';



            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('marketing/newsletter/campaign');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/campaign');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $campaign_id) {
                $this->model_marketing_newsletter_campaign->deleteCampaign($campaign_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';



            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

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
            'href' => $this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/newsletter/campaign/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('marketing/newsletter/campaign/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['campaigns'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $campaigns_total = $this->model_marketing_newsletter_campaign->getTotalCampaigns();

        $results = $this->model_marketing_newsletter_campaign->getCampaigns($filter_data);
        foreach ($results as $result) {
            $result['campaign_name'] = html_entity_decode($result['campaign_name'], ENT_QUOTES, "UTF-8");
            $result['edit'] = $this->url->link('marketing/newsletter/campaign/edit', 'token=' . $this->session->data['token'] . '&campaign_id=' . $result['campaign_id'] . $url, 'SSL');

            $result['member_count'] = $this->model_marketing_newsletter_campaign->getSubscriberCount($result['campaign_id']);
            
            $result['newsletter_count'] = $this->model_marketing_newsletter_campaign->getNewsletterCount($result['campaign_id']);
            
            $data['campaigns'][] = $result;
        }

        $data['heading_title'] = $this->language->get('heading_title');


        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_campaign_name'] = $this->language->get('column_campaign_name');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

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

        $pagination = new \Core\Pagination();
        $pagination->text = $this->language->get('text_pagination');
        $pagination->total = $campaigns_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/campaign_list.phtml', $data));
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['campaign_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['entry_campaign_name'] = $this->language->get('entry_campaign_name');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['campaign_name'])) {
            $data['error_campaign_name'] = $this->error['campaign_name'];
        } else {
            $data['error_campaign_name'] = '';
        }




        $url = '';

       

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
            'href' => $this->url->link('marketing/newsletter/campaign', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['campaign_id'])) {
            $data['action'] = $this->url->link('marketing/newsletter/campaign/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/newsletter/campaign/edit', 'token=' . $this->session->data['token'] . '&campaign_id=' . $this->request->get['campaign_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['campaign_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $campaign_info = $this->model_marketing_newsletter_campaign->getCampaign($this->request->get['campaign_id']);
        }

        $data['token'] = $this->session->data['token'];


        if (isset($this->request->post['campaign_name'])) {
            $data['campaign_name'] = $this->request->post['campaign_name'];
        } elseif (!empty($campaign_info)) {
            $data['campaign_name'] = $campaign_info['campaign_name'];
        } else {
            $data['campaign_name'] = '';
        }

       
        if (isset($this->request->post['campaign_newsletter'])) {
            $data['campaign_newsletter'] = $this->request->post['campaign_newsletter'];
        } elseif (!empty($campaign_info)) {
            $data['campaign_newsletter'] = $campaign_info['campaign_newsletter'];
        } else {
            $data['campaign_newsletter'] = '';
        }
        
        
        

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/campaign_form.phtml', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (utf8_strlen($this->request->post['campaign_name']) < 2 || utf8_strlen($this->request->post['campaign_name']) > 150) {
            $this->error['campaign_name'] = $this->language->get('error_campaign_name');
        }
        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        
        $name = $this->request->get['filter_name'];
        $this->load->model('marketing/newsletter/campaign');
        $filter_data = array(
            'filter_name' => $name,
            'start' => 0,
            'limit' => $this->config->get('config_limit_admin')
        );


        $results = $this->model_marketing_newsletter_campaign->getCampaigns($filter_data);
        
        $json = array();
        foreach ($results as $result) {
            $json[] = array(
                'id' => $result['campaign_id'],
                'name' => strip_tags(html_entity_decode($result['campaign_name'], ENT_QUOTES, 'UTF-8'))
            );
        }


        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
