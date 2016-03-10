<?php

class ControllerMarketingContact extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/contact');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_default'] = $this->language->get('text_default');
        $data['text_newsletter'] = $this->language->get('text_newsletter');
        $data['text_customer_all'] = $this->language->get('text_customer_all');
        $data['text_customer'] = $this->language->get('text_customer');
        $data['text_customer_group'] = $this->language->get('text_customer_group');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_to'] = $this->language->get('entry_to');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_subject'] = $this->language->get('entry_subject');
        $data['entry_message'] = $this->language->get('entry_message');

        $data['help_customer'] = $this->language->get('help_customer');

        $data['button_send'] = $this->language->get('button_send');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/contact', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['cancel'] = $this->url->link('marketing/contact', 'token=' . $this->session->data['token'], 'SSL');



        $this->load->model('sale/customer_group');

        $filter_data = array(
            'filter_system' => 1
        );

        $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups($filter_data);

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/contact.phtml', $data));
    }

    public function send() {
        $this->load->language('marketing/contact');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$this->user->hasPermission('modify', 'marketing/contact')) {
                $json['error']['warning'] = $this->language->get('error_permission');
            }

            if (!$this->request->post['subject']) {
                $json['error']['subject'] = $this->language->get('error_subject');
            }

            if (!$this->request->post['message']) {
                $json['error']['message'] = $this->language->get('error_message');
            }

            if (!$json) {

                $store_name = $this->config->get('config_name');

                $this->load->model('marketing/subscriber');

                $this->load->model('sale/customer');

                $this->load->model('sale/customer_group');

                if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
                } else {
                    $page = 1;
                }

                $email_total = 0;

                $emails = array();

                $override_permission = isset($this->request->post['override_permission']) ? true : false;

                switch ($this->request->post['to']) {
                    case 'newsletter':

                        $customer_data = array(
                            'filter_opt_in' => 1,
                            'start' => ($page - 1) * 10,
                            'limit' => 10
                        );

                        $email_total = $this->model_marketing_subscriber->getTotalSubscribers($customer_data);

                        $results = $this->model_marketing_subscriber->getSubscribers($customer_data);

                        foreach ($results as $result) {
                            $emails[] = $result['email'];
                        }
                        break;
                    case 'customer_all':
                        $customer_data = array(
                            'start' => ($page - 1) * 10,
                            'limit' => 10
                        );

                        if (!$override_permission) {
                            $customer_data['filter_newsletter'] = '1';
                        }

                        $email_total = $this->model_sale_customer->getTotalCustomers($customer_data);

                        $results = $this->model_sale_customer->getCustomers($customer_data);

                        foreach ($results as $result) {
                            $emails[] = $result['email'];
                        }
                        break;
                    case 'customer_group':
                        $customer_data = array(
                            'filter_customer_group_id' => $this->request->post['customer_group_id'],
                            'start' => ($page - 1) * 10,
                            'limit' => 10
                        );

                        if (!$override_permission) {
                            $customer_data['filter_newsletter'] = '1';
                        }

                        $email_total = $this->model_sale_customer->getTotalCustomers($customer_data);

                        $results = $this->model_sale_customer->getCustomers($customer_data);

                        foreach ($results as $result) {
                            $emails[$result['customer_id']] = $result['email'];
                        }
                        break;
                    case 'customer':
                        if (!empty($this->request->post['customer'])) {
                            foreach ($this->request->post['customer'] as $customer_id) {
                                $customer_info = $this->model_sale_customer->getCustomer($customer_id);

                                if ($customer_info && ($override_permission || $customer_info['newsletter'])) {

                                    $emails[] = $customer_info['email'];
                                }
                            }
                        }
                        break;
                }

                $json['emails'] = $emails;
                if ($emails) {
                    $start = ($page - 1) * 10;
                    $end = $start + 10;

                    if ($end < $email_total) {
                        $json['success'] = sprintf($this->language->get('text_sent'), $start, $email_total);
                    } else {
                        $json['success'] = $this->language->get('text_success');
                    }

                    if ($end < $email_total) {
                        $json['next'] = str_replace('&amp;', '&', $this->url->link('marketing/contact/send', 'token=' . $this->session->data['token'] . '&page=' . ($page + 1), 'SSL'));
                    } else {
                        $json['next'] = '';
                    }

                    $message = '<html dir="ltr" lang="en">' . "\n";
                    $message .= '  <head>' . "\n";
                    $message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
                    $message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
                    $message .= '  </head>' . "\n";
                    $message .= '  <body>';
                    if ($this->request->post['preheader']) {
                        $message .= '<span style="display:none;">' . $this->request->post['preheader'] . '</span>';
                    }
                    $message .= html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
                    $message .= '</html>' . "\n";

                    foreach ($emails as $email) {
                        if (preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {

                            $mail = new \Core\Mail();
                           
                            $mail->protocol = $this->config->get('config_mail_protocol');
                            $mail->parameter = $this->config->get('config_mail_parameter');
                            $mail->hostname = $this->config->get('config_mail_smtp_hostname');
                            $mail->username = $this->config->get('config_mail_smtp_username');
                            $mail->password = $this->config->get('config_mail_smtp_password');
                            $mail->port = $this->config->get('config_mail_smtp_port');
                            $mail->timeout = $this->config->get('config_mail_smtp_timeout');
                            $mail->tags = array('old_newsletter');
                            $mail->setTo($email);
                            $mail->setFrom($this->config->get('config_email'));
                            $mail->setSender($store_name);
                            $mail->setSubject($this->request->post['subject']);
                            $mail->setHtml($message);
                            $mail->send();
                        }
                    }
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
