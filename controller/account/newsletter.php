<?php

class ControllerAccountNewsletter extends \Core\Controller {

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/newsletter', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->language('account/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('account/customer');

            $this->model_account_customer->editNewsletter($this->request->post['newsletter']);

            $this->session->data['success'] = $this->language->get('text_success');
            
             // Add to activity log
            $this->load->model('account/activity');

            $activity_data = array(
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                'subscribed' => $this->request->post['newsletter']
            );

            $this->model_account_activity->addActivity('newsletter', $activity_data);

            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_newsletter'),
            'href' => $this->url->link('account/newsletter', '', 'SSL')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_newsletter'] = $this->language->get('entry_newsletter');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        $data['action'] = $this->url->link('account/newsletter', '', 'SSL');

        $data['newsletter'] = $this->customer->getNewsletter();

        $data['back'] = $this->url->link('account/account', '', 'SSL');

        $this->template = 'account/newsletter.phtml';
        $this->data = $data;


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

        $this->response->setOutput($this->render());
    }

}
