<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Newsletter Groups
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingNewsletterGroup extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter/group');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('marketing/newsletter/group');
        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/newsletter/group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_group->addGroup($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';


            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/newsletter/group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_newsletter_group->editGroup($this->request->get['group_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';



            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('marketing/newsletter/group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/newsletter/group');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $group_id) {
                $this->model_marketing_newsletter_group->deleteGroup($group_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';



            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

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
            'href' => $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/newsletter/group/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('marketing/newsletter/group/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['groups'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $groups_total = $this->model_marketing_newsletter_group->getTotalGroups();

        $results = $this->model_marketing_newsletter_group->getGroups($filter_data);
        foreach ($results as $result) {
            $result['group_name'] = html_entity_decode($result['group_name'], ENT_QUOTES, "UTF-8");
            $result['public'] = $result['public'] ? $this->language->get('text_yes') : $this->language->get('text_no');
            $result['edit'] = $this->url->link('marketing/newsletter/group/edit', 'token=' . $this->session->data['token'] . '&group_id=' . $result['group_id'] . $url, 'SSL');

            $result['member_count'] = $this->model_marketing_newsletter_group->getSubscriberCount($result['group_id']);

            $data['groups'][] = $result;
        }

        $data['heading_title'] = $this->language->get('heading_title');


        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_group_name'] = $this->language->get('column_group_name');
        $data['column_public'] = $this->language->get('column_public');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

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

        $url = '';

        $pagination = new \Core\Pagination();
        $pagination->text = $this->language->get('text_pagination');
        $pagination->total = $groups_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/group_list.phtml', $data));
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['group_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['entry_group_name'] = $this->language->get('entry_group_name');
        $data['entry_public'] = $this->language->get('entry_public');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['group_name'])) {
            $data['error_group_name'] = $this->error['group_name'];
        } else {
            $data['error_group_name'] = '';
        }




        $url = '';

       

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
            'href' => $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['group_id'])) {
            $data['action'] = $this->url->link('marketing/newsletter/group/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/newsletter/group/edit', 'token=' . $this->session->data['token'] . '&group_id=' . $this->request->get['group_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/newsletter/group', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $group_info = $this->model_marketing_newsletter_group->getGroup($this->request->get['group_id']);
        }

        $data['token'] = $this->session->data['token'];


        if (isset($this->request->post['group_name'])) {
            $data['group_name'] = $this->request->post['group_name'];
        } elseif (!empty($group_info)) {
            $data['group_name'] = $group_info['group_name'];
        } else {
            $data['group_name'] = '';
        }



        if (isset($this->request->post['public'])) {
            $data['public'] = $this->request->post['public'];
        } elseif (!empty($group_info)) {
            $data['public'] = $group_info['public'];
        } else {
            $data['public'] = 0;
        }
        
        

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/group_form.phtml', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (utf8_strlen($this->request->post['group_name']) < 2 || utf8_strlen($this->request->post['group_name']) > 150) {
            $this->error['group_name'] = $this->language->get('error_group_name');
        }
        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $name = $this->request->get['filter_name'];
        $this->load->model('marketing/newsletter/group');
        $filter_data = array(
            'filter_name' => $name,
            'start' => 0,
            'limit' => $this->config->get('config_limit_admin')
        );


        $results = $this->model_marketing_newsletter_group->getGroups($filter_data);
        $json = array();
        foreach ($results as $result) {
            $json[] = array(
                'id' => $result['group_id'],
                'name' => strip_tags(html_entity_decode($result['group_name'], ENT_QUOTES, 'UTF-8'))
            );
        }


        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
