<?php

class ControllerDashboardActivity extends \Core\Controller {

    public function index() {
        $this->load->language('dashboard/activity');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['token'] = $this->session->data['token'];

        $data['activities'] = array();

        $this->load->model('report/activity');

        $results = $this->model_report_activity->getActivities();

        foreach ($results as $result) {
            if ($result['key'] == 'customer_custom') {
                
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
                'comment' => str_replace($find, $replace, $comment) . '(<a href="http://whatismyipaddress.com/ip/' .$result['ip'] . '" target="_blank">' . $result['ip'] . '</a>)',
                'date_added' => date($this->language->get('date_time_format_short'), strtotime($result['date_added']))
            );
            
        }

        $this->template = 'dashboard/activity.phtml';
        $this->data = $data;
        return $this->render();
    }

}
