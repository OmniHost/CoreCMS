<?php
/**
 * @name Dashboard Widget - At a Glance
 */

class ControllerDashboardStats extends \Core\Controller {

    public function index() {
        $this->load->language('dashboard/stats');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['token'] = $this->session->data['token'];

        $this->load->model('report/customer');
        $this->load->model('sale/customer');

        // Customers Online
        $online_total = $this->model_report_customer->getTotalCustomersOnline();

        if ($online_total > 1000000000000) {
            $data['online_total'] = round($online_total / 1000000000000, 1) . 'T';
        } elseif ($online_total > 1000000000) {
            $data['online_total'] = round($online_total / 1000000000, 1) . 'B';
        } elseif ($online_total > 1000000) {
            $data['online_total'] = round($online_total / 1000000, 1) . 'M';
        } elseif ($online_total > 1000) {
            $data['online_total'] = round($online_total / 1000, 1) . 'K';
        } else {
            $data['online_total'] = $online_total;
        }

        

        $customer_total = $this->model_sale_customer->getTotalCustomers();

        if ($customer_total > 1000000000000) {
            $data['total_users'] = round($customer_total / 1000000000000, 1) . 'T';
        } elseif ($customer_total > 1000000000) {
            $data['total_users'] = round($customer_total / 1000000000, 1) . 'B';
        } elseif ($customer_total > 1000000) {
            $data['total_users'] = round($customer_total / 1000000, 1) . 'M';
        } elseif ($customer_total > 1000) {
            $data['total_users'] = round($customer_total / 1000, 1) . 'K';
        } else {
            $data['total_users'] = $customer_total;
        }



        $query = $this->db->query("select count(*) as total from #__ams_pages where namespace='cms.page' and status='1'");
        $data['total_pages'] = $query->row['total'];
        $query = $this->db->query("select count(*) as total from #__ams_pages where namespace='blog.post' and status='1'");
        $data['total_posts'] = $query->row['total'];
        
        $query = $this->db->query("select count(*) as total from #__contact");
        $data['total_contacts'] = $query->row['total'];


        $this->load->model('cms/comment');
        $data['total_comments'] = $this->model_cms_comment->getTotalComments(array('filter_status' => '1'));



        $data['text_online'] = $this->language->get('text_online');
        $data['text_users'] = $this->language->get('text_users');
        $data['text_pages'] = $this->language->get('text_pages');
        $data['text_posts'] = $this->language->get('text_posts');
        $data['text_comments'] = $this->language->get('text_comments');
        $data['text_contacts'] = $this->language->get('text_contacts');
        
        $data['online'] = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], 'SSL');
        $data['users'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL');
$data['pages'] = $this->url->link('cms/page', 'token=' . $this->session->data['token'], 'SSL');
        $data['posts'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'], 'SSL');
 $data['comments'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'], 'SSL');
 $data['contacts'] = $this->url->link('sale/contact', 'token=' . $this->session->data['token'], 'SSL');


        $this->template = 'dashboard/stats.phtml';
        $this->data = $data;
        return $this->render();
    }

}
