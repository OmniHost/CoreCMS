<?php

class ControllerCommonContact extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('common/contact');

        $this->document->setTitle($this->language->get('heading_title'));
        
         

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
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
             foreach($fields as $cfield){
                if($cfield['location'] == 'contact'){
                    $mailbody .= $cfield['name']. ": " . $this->request->post['custom_field'][$cfield['custom_field_id']] . "\n";
                    $post['custom_field'][$cfield['name']] = $this->request->post['custom_field'][$cfield['custom_field_id']];

                }
             }
            
         
            
            
            $mailbody .= "\n\n\n------------------------------------------\n";
            $mailbody .= "" . DATE("Y-m-d h:i a") . " | " . $this->request->server['REMOTE_ADDR'];
            
            

            $mailbody = \Core\HookPoints::executeHooks('contact_form_submit_body', $mailbody, $this->request->post);
            $mailsubject = sprintf($this->language->get('email_subject'), $this->request->post['name']);
            $mailsubject = \Core\HookPoints::executeHooks('contact_form_submit_subject', $mailsubject, $this->request->post);

            $this->load->model('account/contact');
            $this->model_account_contact->addContact($post);
         

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->request->post['email']);
            $mail->setSender($this->request->post['name']);
            $mail->setSubject();
            $mail->setText($mailbody);
            $mail->send();

            $this->redirect($this->url->link('common/contact/success'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/contact')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_location'] = $this->language->get('text_location');
        $data['text_store'] = $this->language->get('text_store');
        $data['text_contact'] = $this->language->get('text_contact');
        $data['text_address'] = $this->language->get('text_address');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');
        $data['text_comment'] = $this->language->get('text_comment');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_enquiry'] = $this->language->get('entry_enquiry');
        $data['entry_captcha'] = $this->language->get('entry_captcha');

        $data['button_map'] = $this->language->get('button_map');

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['enquiry'])) {
            $data['error_enquiry'] = $this->error['enquiry'];
        } else {
            $data['error_enquiry'] = '';
        }

        if (isset($this->error['captcha'])) {
            $data['error_captcha'] = $this->error['captcha'];
        } else {
            $data['error_captcha'] = '';
        }

        if (isset($this->error['custom_field'])) {
            $data['error_custom_field'] = $this->error['custom_field'];
        } else {
            $data['error_custom_field'] = array();
        }

        $data['button_submit'] = $this->language->get('button_submit');

        $data['action'] = $this->url->link('common/contact');
        /*
          $this->load->model('tool/image');

          if ($this->config->get('config_image')) {
          $data['image'] = $this->model_tool_image->resize($this->config->get('config_image'), $this->config->get('config_image_location_width'), $this->config->get('config_image_location_height'));
          } else {
          $data['image'] = false;
          }
         */

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

        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->template = 'common/contact.phtml';

        $this->data = $data;
        $this->response->setOutput($this->render());
    }

    public function success() {
        $this->load->language('common/contact');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/contact')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_message'] = $this->language->get('text_success');

        $data['button_continue'] = $this->language->get('button_continue');

        $data['continue'] = $this->url->link('common/home');

        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->template = 'common/success.phtml';

        $this->data = $data;
        $this->response->setOutput($this->render());
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
