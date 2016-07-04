<?php

class ControllerToolCron extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('tool/cron');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/cron');



        $this->getList();
    }

    public function delete() {
        $this->load->language('tool/cron');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/cron');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $cron_id) {


                $this->model_tool_cron->deleteCron($cron_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';


            $this->redirect($this->url->link('tool/cron', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/cron', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['delete'] = $this->url->link('tool/cron/delete', 'token=' . $this->session->data['token'], 'SSL');
        $data['insert'] = $this->url->link('tool/cron/insert', 'token=' . $this->session->data['token'], 'SSL');

        $data['crons'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $cron_total = $this->model_tool_cron->getTotalCrons($filter_data);

        $results = $this->model_tool_cron->getCrons($filter_data);

        foreach ($results as $result) {
            $data['crons'][] = array(
                'cron_id' => $result['cron_id'],
                'name' => $result['name'],
                'time' => json_decode($result['time'], 1),
                'params' => json_decode($result['time'], 1),
                'route' => $result['route']
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_filename'] = $this->language->get('column_filename');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_download'] = $this->language->get('button_download');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

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




        $pagination = new \Core\Pagination();
        $pagination->text = $this->language->get('text_pagination');
        $pagination->total = $cron_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('tool/cron', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();





        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('tool/crons.phtml', $data));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'tool/cron')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}
