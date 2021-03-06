<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Reports - Customer activity
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
class ControllerReportCustomerActivity extends \Core\Controller {

    public function index() {
        $this->load->language('report/customer_activity');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_ip'])) {
            $filter_ip = $this->request->get['filter_ip'];
        } else {
            $filter_ip = null;
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = '';
        }

        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } else {
            $filter_date_end = '';
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

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
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
            'href' => $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'text' => $this->language->get('heading_title')
        );

        $this->load->model('report/customer');

        $data['activities'] = array();

        $filter_data = array(
            'filter_customer' => $filter_customer,
            'filter_ip' => $filter_ip,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'start' => ($page - 1) * 20,
            'limit' => 20
        );

        $activity_total = $this->model_report_customer->getTotalCustomerActivities($filter_data);

        $results = $this->model_report_customer->getCustomerActivities($filter_data);

        foreach ($results as $result) {

            if ($result['key'] == 'custom') {
                
                $activity = unserialize($result['data']);
                
                 $comment = str_replace("%TOKEN%", $this->session->data['token'],vsprintf($activity['comment'],$activity));
                
                
            } else {

                $comment = vsprintf($this->language->get('text_' . $result['key']), unserialize($result['data']));

            }
                $find = array(
                    'customer_id='
                );

                $replace = array(
                    $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=', 'SSL'),
                );

                $data['activities'][] = array(
                    'comment' => str_replace($find, $replace, $comment),
                    'ip' => '<a href="http://whatismyipaddress.com/ip/' .$result['ip'] . '" target="_blank">' . $result['ip'] . '</a>',
                    'date_added' => date($this->language->get('date_time_format_long'), strtotime($result['date_added']))
                );
            
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_comment'] = $this->language->get('column_comment');
        $data['column_ip'] = $this->language->get('column_ip');
        $data['column_date_added'] = $this->language->get('column_date_added');

        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_ip'] = $this->language->get('entry_ip');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $url = '';

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode($this->request->get['filter_customer']);
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $activity_total;
        $pagination->page = $page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();


        $data['filter_customer'] = $filter_customer;
        $data['filter_ip'] = $filter_ip;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;

        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');
 

        $this->data = $data;
        $this->template = 'report/customer_activity.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

}
