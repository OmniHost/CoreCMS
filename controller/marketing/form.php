<?php

class ControllerMarketingForm extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('marketing/form');

        $this->load->model('marketing/form');

        if (isset($this->request->get['form_id'])) {
            $form_id = $this->request->get['form_id'];
        } else {
            $form_id = 0;
        }

        if (isset($this->request->get['redirect'])) {
            $redirect = $this->request->get['redirect'];
        } else {
            $redirect = false;
        }

        $form_info = $this->model_marketing_form->getForm($form_id);

        $data['success_msg'] = $form_info['success_msg'];
        $form_email = $form_info['email'];
        $form_url = $form_info['url'];
        $data['heading_title'] = $heading_title = $form_info['title'];
        $this->document->setTitle($heading_title);

        $data['formdata'] = $this->model_marketing_form->getFormShow(unserialize($form_info['formdata']));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($data['formdata'])) {
            //  $parameter = $_POST;

            /*       ob_start();
              require_once('template_mail.php');
              $content = ob_get_contents(); */

            $tpl = new \Core\Template();
            $tpl->data['postdata'] = $data['formdata'];
            $tpl->data['formname'] = $form_info['title'];
            $content = $tpl->fetch('marketing/form_mail.phtml');






            $mail = new \Core\Mail();
            $mail->tags = array('Custom Form - ' . $form_info['form_id']);

            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->username = $this->config->get('config_mail_smtp_username');
            $mail->password = $this->config->get('config_mail_smtp_password');
            $mail->port = $this->config->get('config_mail_smtp_port');
            $mail->timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($form_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject(sprintf($heading_title));

            foreach ($this->request->files as $file) {
                if (!empty($file['tmp_name'])) {
                    $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8')));
                    $filestore = $filename . '.' . md5(mt_rand());
                    move_uploaded_file($file['tmp_name'], DIR_UPLOAD . $filestore);
                    $mail->addAttachment(DIR_UPLOAD . $filestore, $filename);
                }
            }

        

            $mail->setHtml($content);
            $mail->send();




            $this->redirect($this->url->link('marketing/form/success', 'id=' . $form_id . '&url=' . urlencode($_SERVER['REQUEST_URI'])));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $heading_title,
            'href' => $this->url->link('marketing/form', 'form_id=' . $form_id),
            'separator' => $this->language->get('text_separator')
        );


        $data['text_location'] = $this->language->get('text_location');
        $data['text_creator'] = $this->language->get('text_creator');
        $data['text_address'] = $this->language->get('text_address');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_enquiry'] = $this->language->get('entry_enquiry');
        $data['entry_captcha'] = $this->language->get('entry_captcha');



        if (isset($this->error['error_captcha'])) {
            $this->session->data['error_captcha'] = $data['error_captcha'] = $this->error['error_captcha'];
        } else {
            $this->session->data['error_captcha'] = $data['error_captcha'] = '';
        }

        if (isset($this->error['email'])) {
            $this->session->data['email'] = $data['email'] = $this->error['email'];
        } else {
            $this->session->data['email'] = $data['email'] = '';
        }

        if ($redirect != false) {
            $this->redirect(base64_decode($redirect));
        }



        $data['button_continue'] = $this->language->get('button_continue');

        $data['action'] = $this->url->link('marketing/form', 'form_id=' . $this->request->get['form_id'], 'SSL');





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

        $this->template = 'marketing/form.phtml';

        $this->data = $data;

        $this->response->setOutput($this->render());
    }

    public function success() {
        $this->language->load('marketing/form');

        $this->load->model('marketing/form');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['id'])) {
            $form_id = $this->request->get['id'];
        } else {
            $form_id = 0;
        }


        $form_info = $this->model_marketing_form->getForm($form_id);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $form_info['title'],
            'href' => $this->url->link('marketing/form', 'form_id=' . $form_id),
            'separator' => $this->language->get('text_separator')
        );

        $data['heading_title'] = $form_info['title'];

        $data['text_message'] = $form_info['success_msg'];

        $data['button_continue'] = $this->language->get('button_continue');

        $data['continue'] = str_replace('&amp;', '&', urldecode($this->request->get['url']));

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

    public function captcha() {
        $this->load->library('captcha');

        $captcha = new Captcha();


        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

    private function validate(&$formData) {

        foreach ($formData as $idx => &$field) {
            if ($field['required'] && empty($field['post'])) {
                $this->error[$field['name']] = $this->language->get("This field is required");
                $field['error'] = $this->language->get("This field is required");
            }
            if ($field['type'] == 'email' && !empty($field['post']) && !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $field['post'])) {
                $this->error[$field['name']] = $this->language->get('error_email');
                $field['error'] = $this->language->get('error_email');
            }
            if ($field['type'] == 'captcha') {
                if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $field['post'])) {
                    $this->error[$field['name']] = $this->language->get('error_captcha');
                    $field['error'] = $this->language->get('error_captcha');
                }
            }
        }
        /*   debugPre($formData);
          debugPre($this->error);
          exit;

          foreach ($this->request->post as $key => $val) {
          if (strpos($key, "_1_")) {
          list($type, $name) = explode("_1_", $key);


          if ($type == 'email')
          if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post[$key])) {
          $this->error['email'] = $this->language->get('error_email');
          }

          if ($type == 'captcha')
          if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post[$key])) {
          $this->error['error_captcha'] = $this->language->get('error_captcha');
          }
          }
          } */
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
