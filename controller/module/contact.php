<?php

class ControllerModuleContact extends \Core\Controller {

    private $error = array();

    public function index() {

        $this->load->language('common/contact');


        $data['heading_title'] = $this->language->get('heading_title');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_enquiry'] = $this->language->get('entry_enquiry');
        $data['entry_captcha'] = $this->language->get('entry_captcha');

        $data['button_map'] = $this->language->get('button_map');



        $data['button_submit'] = $this->language->get('button_submit');

        $data['action'] = $this->url->link('module/contact');


        $data['store'] = $this->config->get('config_name');


        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } else {
            $data['name'] = $this->customer->getFirstName();
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = $this->customer->getEmail();
        }

        if (isset($this->request->post['enquiry'])) {
            $data['enquiry'] = $this->request->post['enquiry'];
        } else {
            $data['enquiry'] = '';
        }

        if (isset($this->request->post['captcha'])) {
            $data['captcha'] = $this->request->post['captcha'];
        } else {
            $data['captcha'] = '';
        }


        $this->load->model('account/custom_field');

        $data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

        if (isset($this->request->post['custom_field'])) {
            $data['contact_custom_field'] = $this->request->post['custom_field'];
        } else {
            $data['contact_custom_field'] = array();
        }

        $this->document->addScript('view/plugins/datetimepicker/moment.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');


        $this->data = $data;

        $this->template = 'module/contact.phtml';
        return $this->render();
    }

    public function submit() {


        $this->load->language('common/contact');

        if ($this->validate()) {
            unset($this->session->data['captcha']);

            $this->language->load('common/contact');



            $mail = new \Core\Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->username = $this->config->get('config_mail_smtp_username');
            $mail->password = $this->config->get('config_mail_smtp_password');
            $mail->port = $this->config->get('config_mail_smtp_port');
            $mail->timeout = $this->config->get('config_mail_smtp_timeout');

            $post = $this->request->post;
            unset($post['custom_field']);

            $mailbody = "Contact form Submission \n";

            $mailbody .= $this->language->get('entry_name') . ": " . $this->request->post['name'] . "\n";
            $mailbody .= $this->language->get('entry_email') . ": " . $this->request->post['email'] . "\n";
            $mailbody .= $this->language->get('entry_enquiry') . ": " . $this->request->post['enquiry'] . "\n";

            $fields = $this->model_account_custom_field->getCustomFields();
            foreach ($fields as $cfield) {
                if ($cfield['location'] == 'contact') {
                    $mailbody .= $cfield['name'] . ": " . $this->request->post['custom_field'][$cfield['custom_field_id']] . "\n";
                    $post['custom_field'][$cfield['name']] = $this->request->post['custom_field'][$cfield['custom_field_id']];
                }
            }




            $mailbody .= "\n\n\n------------------------------------------\n";
            $mailbody .= "" . DATE("Y-m-d h:i a") . " | " . $this->request->server['REMOTE_ADDR'];





            $this->event->trigger('contact.form.submit.body', $mailbody);

            $mailsubject = sprintf($this->language->get('email_subject'), $this->request->post['name']);

            $this->event->trigger('contact.form.submit.subject', $mailsubject);

            $this->load->model('account/contact');
            $this->model_account_contact->addContact($post);


            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->request->post['email']);
            $mail->setSender($this->request->post['name']);
            $mail->setSubject($mailsubject);
            $mail->setText($mailbody);
            
            
            $mail->send();

            $data['success'] = $this->language->get('text_success');
            //   $this->redirect($this->url->link('common/contact/success'));
        } else {
            $data['error'] = $this->error;
            
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    protected function validate() {
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
            $this->error['enquiry'] = $this->language->get('error_enquiry');
        }

        if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
            $this->error['captcha'] = $this->language->get('error_captcha');
        }

        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

        foreach ($custom_fields as $custom_field) {
            if (($custom_field['location'] == 'contact') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
                $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            }
        }



        return !$this->error;
    }

}
