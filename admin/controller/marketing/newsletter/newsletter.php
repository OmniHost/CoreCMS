<?php

class ControllerMarketingNewsletterNewsletter extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter');

        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            

            
            $this->model_marketing_newsletter->addNewsletter($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter->editNewsletter($this->request->get['newsletter_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('marketing/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $newsletter_id) {
                $this->model_marketing_newsletter->deleteNewsletter($newsletter_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'create_date';
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
            'href' => $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/newsletter/newsletter/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('marketing/newsletter/newsletter/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['newsletters'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $newsletters_total = $this->model_marketing_newsletter->getTotalNewsletters($filter_data);

        $results = $this->model_marketing_newsletter->getNewsletters($filter_data);



        foreach ($results as $result) {
            $result['sends'] = $this->model_marketing_newsletter->getSendCount($result['newsletter_id']);
            $result['opens'] = $this->model_marketing_newsletter->getOpenCount($result['newsletter_id']);
            $result['unsubscribes'] = $this->model_marketing_newsletter->getUnsubscribeCount($result['newsletter_id']);
            $result['bounces'] = $this->model_marketing_newsletter->getBounceCount($result['newsletter_id']);

            $result['edit'] = $this->url->link('marketing/newsletter/newsletter/edit', 'token=' . $this->session->data['token'] . '&newsletter_id=' . $result['newsletter_id'] . $url, 'SSL');
            $result['send'] = $this->url->link('marketing/newsletter/send', 'token=' . $this->session->data['token'] . '&newsletter_id=' . $result['newsletter_id'] . $url, 'SSL');
            $result['stats'] = $this->url->link('marketing/newsletter/newsletter/stats', 'token=' . $this->session->data['token'] . '&newsletter_id=' . $result['newsletter_id'] . $url, 'SSL');
            $data['newsletters'][] = $result;
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_subject'] = $this->language->get('column_subject');
        $data['column_created'] = $this->language->get('column_created');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_date'] = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . '&sort=create_date' . $url, 'SSL');
        $data['sort_name'] = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $data['sort_subject'] = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . '&sort=subject' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $newsletters_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
        $pagination->text = $this->language->get('text_pagination');

        $data['pagination'] = $pagination->render();


        $data['sort'] = $sort;
        $data['order'] = $order;

        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->data = $data;
        $this->template = 'marketing/newsletter_list.phtml';
        $this->response->setOutput($this->render());
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');



        $data['entry_newsletter_name'] = $this->language->get('entry_newsletter_name');
        $data['entry_newsletter_subject'] = $this->language->get('entry_newsletter_subject');
        $data['entry_newsletter_from_name'] = $this->language->get('entry_newsletter_from_name');
        $data['entry_newsletter_from_email'] = $this->language->get('entry_newsletter_from_email');
        $data['entry_newsletter_bounce_email'] = $this->language->get('entry_newsletter_bounce_email');



        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['newsletter_name'])) {
            $data['error_newsletter_name'] = $this->error['newsletter_name'];
        } else {
            $data['error_newsletter_name'] = '';
        }

        if (isset($this->error['subject'])) {
            $data['error_newsletter_subject'] = $this->error['subject'];
        } else {
            $data['error_newsletter_subject'] = '';
        }

        if (isset($this->error['from_name'])) {
            $data['error_newsletter_from'] = $this->error['from_name'];
        } else {
            $data['error_newsletter_from'] = '';
        }

        if (isset($this->error['from_email'])) {
            $data['error_newsletter_from_email'] = $this->error['from_email'];
        } else {
            $data['error_newsletter_from_email'] = '';
        }

        if (isset($this->error['bounce_email'])) {
            $data['error_newsletter_bounce_email'] = $this->error['bounce_email'];
        } else {
            $data['error_newsletter_bounce_email'] = '';
        }

        $url = '';

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
            'href' => $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['newsletter_id'])) {
            $data['action'] = $this->url->link('marketing/newsletter/newsletter/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/newsletter/newsletter/edit', 'token=' . $this->session->data['token'] . '&newsletter_id=' . $this->request->get['newsletter_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['newsletter_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $newsletter_info = $this->model_marketing_newsletter->getNewsletter($this->request->get['newsletter_id']);
        }


        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['name'] = $newsletter_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['subject'])) {
            $data['subject'] = $this->request->post['subject'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['subject'] = $newsletter_info['subject'];
        } else {
            $data['subject'] = '';
        }

        if (isset($this->request->post['from_name'])) {
            $data['from_name'] = $this->request->post['from_name'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['from_name'] = $newsletter_info['from_name'];
        } else {
            $data['from_name'] = $this->config->get('config_name');
        }

        if (isset($this->request->post['from_email'])) {
            $data['from_email'] = $this->request->post['from_email'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['from_email'] = $newsletter_info['from_email'];
        } else {
            $data['from_email'] = $this->config->get('config_email');
        }

        if (isset($this->request->post['bounce_email'])) {
            $data['bounce_email'] = $this->request->post['bounce_email'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['bounce_email'] = $newsletter_info['bounce_email'];
        } else {
            $data['bounce_email'] = $this->config->get('config_bounce_email');
        }
        
         if (isset($this->request->post['builder_info'])) {
            $data['builder_info'] = $this->request->post['builder_info'];
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['builder_info'] = json_decode($newsletter_info['template'],1);
        } else {
            $data['builder_info'] = array(
                'template' => 'basic',
                'html' => '',
                'styles' => ''
            );
        }
        
      //  debugPre($data);
     //   exit;
        
        if (isset($this->request->post['content'])) {
            $data['content'] = html_entity_decode($this->request->post['content']);
        } elseif (isset($this->request->get['newsletter_id'])) {
            $data['content'] = html_entity_decode($newsletter_info['content']);
        } else {
            $data['content'] = '';
        }
        
        
        $data['templates'] = array();
        $folders = glob(DIR_APPLICATION . 'view/plugins/dentifrice/templates/*', GLOB_ONLYDIR);
        $this->load->model('tool/image');
        foreach($folders as $folder){
            $tname = basename($folder);
            if($tname[0] == '.' || $tname[0] == '_'){
                continue;
            }else{
                $image = (is_file($folder . '/preview.jpg'))? 'view/plugins/dentifrice/templates/' . $tname . '/preview.jpg' : $this->model_tool_image->resize('no_image.jpg', 150,150);
                $data['templates'][] = array(
                    'key' => $tname,
                    'name' => ucfirst($tname),
                    'preview' => $image,
                    'path' => 'templates/' . $tname . '/' . $tname . '.html'
                );
            }
            
        }
        $this->document->addStyle('view/plugins/dentifrice/css/dentifrice.css');
        $this->document->addScript('view/plugins/dentifrice/dentifrice.js');

        $this->data = $data;
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->template = 'marketing/newsletter_form.phtml';


        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 100)) {
            $this->error['newsletter_name'] = $this->language->get('error_name');
        }
        if ((utf8_strlen($this->request->post['subject']) < 3) || (utf8_strlen($this->request->post['subject']) > 75)) {
            $this->error['subject'] = $this->language->get('error_subject');
        }

        if ((utf8_strlen($this->request->post['from_name']) < 3) || (utf8_strlen($this->request->post['from_name']) > 75)) {
            $this->error['from_name'] = $this->language->get('error_from_name');
        }

        if ((utf8_strlen($this->request->post['from_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['from_email'])) {
            $this->error['from_email'] = $this->language->get('error_from_email');
        }

        if ((utf8_strlen($this->request->post['bounce_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['bounce_email'])) {
            $this->error['bounce_email'] = $this->language->get('error_bounce_email');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }



        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {

            $this->load->model('marketing/newsletter');


            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_marketing_newsletter->getNewsletters($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'id' => $result['newsletter_id'],
                    'newsletter_id' => $result['newsletter_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    
    public function preview(){
        $html = html_entity_decode($this->request->post['newsletter']);
        $this->load->model('marketing/newsletter');
        $newsletter_html = $this->model_marketing_newsletter->fix_image_paths($html);
        

        $replace = array(
                "email_subject" => 'Preview - ' . $this->request->post['subject'],
                "from_name" => $this->request->post['from_name'],
                "from_email" => $this->request->post['from_email'],
                "to_email" => $this->request->post['to_email'],
                "sent_date" => date("jS M, Y"),
                "sent_month" => date("M Y"),
                "unsubscribe" => $this->config->get('config_url') . 'ext/unsubcribe#',
                "view_online" => $this->config->get('config_url') . 'ext/view#',
                /*   "link_account" => $this->settings['url_update'], */
                "member_id" => 0,
                "send_id" => 0,
                "MEMBER_HASH" => md5("Member Hash for , with member_id ,"),
                "first_name" => "John",
                "last_name" => "Smith",
                "email" => $this->request->post['to_email'],
            );
        
        foreach ($replace as $key => $val) {
                $newsletter_html = preg_replace('/\{' . strtoupper(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/' . strtoupper(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/\{' . strtolower(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/' . strtolower(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
            }
            
            $options = array(
                "bounce_email" => $this->request->post['bounce_email'],
                "message_id" => "Newsletter-Preview",
            );
            
              $send_email_status = $this->send_email($replace['to_email'], $replace['email_subject'], $newsletter_html, $replace['from_email'], $replace['from_name'], $options);

    }
    
    protected function send_email($to_email, $email_subject, $newsletter_html, $from_email, $from_name, $options = array()) {

        $mail = new \Core\Mail();
        $mail->tags = array('Newsletter System');
        
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->username = $this->config->get('config_mail_smtp_username');
        $mail->password = $this->config->get('config_mail_smtp_password');
        $mail->port = $this->config->get('config_mail_smtp_port');
        $mail->timeout = $this->config->get('config_mail_smtp_timeout');
        if (!empty($options['message_id'])) {
            $mail->message_id = $options['message_id'];
        }
        $mail->setFrom($this->config->get('config_email'));
        if (!empty($options['bounce_email'])) {
            $mail->setFrom($options['bounce_email']);
        }
        $mail->setTo($to_email);

        $mail->setReplyTo($from_email, $from_name);
        $mail->setSender($from_name);
        $mail->setSubject($email_subject);
        $mail->setHtml($newsletter_html);

        try {
            $mail->send();
            return true;
        } catch (\Core\Exception $e) {

            return false;
        }
    }

    
}
