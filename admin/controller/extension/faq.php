<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Content - FAQ System
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
class ControllerExtensionFaq extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('extension/faq');

        $this->load->model('extension/faq');

        $this->document->setTitle($this->language->get('heading_title'));

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/faq', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];

            unset($this->error['warning']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
            $data['filter_name'] = $this->request->get['filter_name']; //for template
        } else {
            $filter_name = null;
            $data['filter_name'] = ''; //for template
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
            $data['filter_date_added'] = $this->request->get['filter_date_added']; //for template
        } else {
            $filter_date_added = null;
            $data['filter_date_added'] = ''; //for template
        }




        $url = '';

        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_date_added' => $filter_date_added,
            'page' => $page,
            'limit' => $this->config->get('config_limit_admin'),
            'start' => $this->config->get('config_limit_admin') * ($page - 1),
        );

        $total = $this->model_extension_faq->getTotalfaq($filter_data);

        $pagination = new \Core\Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/faq', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['heading_title'] = $this->language->get('heading_title');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view_all_faq'] = $this->language->get('button_view_all_faq');
        $data['text_title'] = $this->language->get('text_title');
        $data['text_question'] = $this->language->get('text_question');
        $data['text_answer'] = $this->language->get('text_answer');
        $data['text_date'] = $this->language->get('text_date');
        $data['text_action'] = $this->language->get('text_action');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_delete'] = $this->language->get('button_delete');

        $data['token'] = $this->session->data['token'];



        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['add'] = $this->url->link('extension/faq/insert', '&token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('extension/faq/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['all_faq'] = array();

        $all_faq = $this->model_extension_faq->getAllfaq($filter_data);

        foreach ($all_faq as $faq) {
            $data['all_faq'][] = array(
                'faq_id' => $faq['faq_id'],
                'question' => $faq['question'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($faq['date_added'])),
                'edit' => $this->url->link('extension/faq/edit', 'faq_id=' . $faq['faq_id'] . '&token=' . $this->session->data['token'] . $url, 'SSL')
            );
        }

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');
        
      
                $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');
       // $this->document->addScript('view/plugins/jQueryUI/jquery-ui.js');

        $this->response->setOutput($this->render('extension/faq_list.phtml', $data));
    }

    public function edit() {
        $this->language->load('extension/faq');

        $this->load->model('extension/faq');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_faq->editfaq($this->request->get['faq_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/faq', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->form();
    }

    public function insert() {
        $this->language->load('extension/faq');

        $this->load->model('extension/faq');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_faq->addfaq($this->request->post);



            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/faq', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->form();
    }

    protected function form() {
        $this->language->load('extension/faq');

        $this->load->model('extension/faq');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/faq', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['faq_id'])) {
            $data['heading_title'] = $this->language->get('heading_title_update');
            $data['action'] = $this->url->link('extension/faq/edit', '&faq_id=' . $this->request->get['faq_id'] . '&token=' . $this->session->data['token'], 'SSL');
        } else {
            $data['action'] = $this->url->link('extension/faq/insert', '&token=' . $this->session->data['token'], 'SSL');
            $data['heading_title'] = $this->language->get('heading_title_add');
        }

        $data['cancel'] = $this->url->link('extension/faq', '&token=' . $this->session->data['token'], 'SSL');

        


        $data['text_title'] = $this->language->get('text_title');
        $data['text_question'] = $this->language->get('text_question');
        $data['text_answer'] = $this->language->get('text_answer');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_keyword'] = $this->language->get('text_keyword');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['token'] = $this->session->data['token'];


        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        } else {
            $data['error'] = '';
        }

        if (isset($this->request->get['faq_id'])) {
            $faq = $this->model_extension_faq->getfaq($this->request->get['faq_id']);
        } else {
            $faq = array();
        }

        if (isset($this->request->post['question'])) {
            $data['question'] = $this->request->post['faq_question'];
        } elseif (!empty($faq)) {
            $data['question'] = $faq['question'];
        } else {
            $data['question'] = '';
        }

        if (isset($this->request->post['answer'])) {
            $data['answer'] = $this->request->post['faq_question'];
        } elseif (!empty($faq)) {
            $data['answer'] = $faq['answer'];
        } else {
            $data['answer'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($faq)) {
            $data['status'] = $faq['status'];
        } else {
            $data['status'] = '';
        }
        
        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($faq)) {
            $data['sort_order'] = $faq['sort_order'];
        } else {
            $data['sort_order'] = '0';
        }

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('extension/faq_form.phtml', $data));
    }

    public function delete() {
        $this->language->load('extension/faq');

        $this->load->model('extension/faq');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $faq_id) {
                $this->model_extension_faq->deletefaq($faq_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->redirect($this->url->link('extension/faq', 'token=' . $this->session->data['token'], 'SSL'));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/faq')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/faq')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
