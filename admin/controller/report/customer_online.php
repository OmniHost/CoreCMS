<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Reports - Customers Online
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerReportCustomerOnline extends \Core\Controller {

    public function index() {
        $this->load->language('report/customer_online');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_ip'])) {
            $filter_ip = $this->request->get['filter_ip'];
        } else {
            $filter_ip = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode($this->request->get['filter_customer']);
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home')
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('report/customer_online', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'text' => $this->language->get('heading_title')
        );

        $this->load->model('report/customer');
        $this->load->model('sale/customer');

        $data['customers'] = array();

        $filter_data = array(
            'filter_ip' => $filter_ip,
            'filter_customer' => $filter_customer,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $customer_total = $this->model_report_customer->getTotalCustomersOnline($filter_data);

        $results = $this->model_report_customer->getCustomersOnline($filter_data);

        foreach ($results as $result) {
            $customer_info = $this->model_sale_customer->getCustomer($result['customer_id']);

            if ($customer_info) {
                $customer = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
            } else {
                $customer = $this->language->get('text_guest');
            }

            $data['customers'][] = array(
                'customer_id' => $result['customer_id'],
                'ip' => $result['ip'],
                'customer' => $customer,
                'url' => $result['url'],
                'referer' => $result['referer'],
                'date_added' => date($this->language->get('date_time_format_long'), strtotime($result['date_added'])),
                'edit' => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_ip'] = $this->language->get('column_ip');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_url'] = $this->language->get('column_url');
        $data['column_referer'] = $this->language->get('column_referer');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_ip'] = $this->language->get('entry_ip');
        $data['entry_customer'] = $this->language->get('entry_customer');

        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $url = '';

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode($this->request->get['filter_customer']);
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $customer_total;
        $pagination->page = $page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();


        $data['filter_customer'] = $filter_customer;
        $data['filter_ip'] = $filter_ip;

        
        $this->template = 'report/customer_online.phtml';
        $this->data = $data;
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

}
