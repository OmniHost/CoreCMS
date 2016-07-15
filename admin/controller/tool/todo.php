<?php

/* CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Tools - ToDo System
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerToolTodo extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('tool/todo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/todo');

        $this->getList();
    }

    protected function getList() {


        $this->document->addStyle('view/plugins/magnific/magnific-popup.css');
        $this->document->addScript('view/plugins/magnific/jquery.magnific-popup.min.js');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_filter_queue_awaiting_decision'] = $this->language->get('text_filter_queue_awaiting_decision');
        $data['text_filter_queue_awaiting_answer'] = $this->language->get('text_filter_queue_awaiting_answer');
        $data['text_filter_queue_inwork'] = $this->language->get('text_filter_queue_inwork');
        $data['text_filter_queue_myqueue'] = $this->language->get('text_filter_queue_myqueue');
        $data['text_filter_queue_not_distributed'] = $this->language->get('text_filter_queue_not_distributed');
        $data['text_filter_queue_finished'] = $this->language->get('text_filter_queue_finished');

        $data['text_role_author'] = $this->language->get('text_role_author');
        $data['text_role_contractor'] = $this->language->get('text_role_contractor');

        $data['text_priority_1'] = $this->language->get('text_priority_1');
        $data['text_priority_2'] = $this->language->get('text_priority_2');
        $data['text_priority_3'] = $this->language->get('text_priority_3');
        $data['text_priority_4'] = $this->language->get('text_priority_4');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_add'] = $this->language->get('text_add');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['column_description'] = $this->language->get('column_description');
        $data['column_author'] = $this->language->get('column_author');
        $data['column_contractor'] = $this->language->get('column_contractor');
        $data['column_priority'] = $this->language->get('column_priority');
        $data['column_deadline'] = $this->language->get('column_deadline');
        $data['column_queue'] = $this->language->get('column_queue');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_queue'] = $this->language->get('entry_queue');
        $data['entry_role'] = $this->language->get('entry_role');
        $data['entry_suspended'] = $this->language->get('entry_suspended');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_log'] = $this->language->get('button_log');

        $data['token'] = $this->session->data['token'];
        $data['config_autofilter'] = $this->config->get('model_todo_autofilter');

        // Set defaults
        $filter_priority = null;
        $filter_queue = null;
        $filter_role = 'contractor';
        $filter_suspended = '0';
        $sort = '';
        $order = 'ASC';
        $page = 1;

        // Check and set parameters (redefine defaults)
        $get_params = array(
            'filter_priority',
            'filter_queue',
            'filter_role',
            'filter_suspended',
            'sort',
            'order',
            'page'
        );

        foreach ($get_params as $get_param) {

            if (isset($this->request->get[$get_param])) {
                $$get_param = $this->request->get[$get_param];
            }
        }

        // $query_filter
        $get_params = array(
            'filter_priority',
            'filter_queue',
            'filter_role',
            'filter_suspended'
        );
        $query_filter = '';
        foreach ($get_params as $get_param) {
            if (isset($this->request->get[$get_param])) {
                $query_filter .= '&' . $get_param . '=' . $this->request->get[$get_param];
            }
        }

        $url = $query_filter;

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
            'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['insert'] = $this->url->link('tool/todo/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('tool/todo/complete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['tasks'] = array();

        $filter_data = array(
            'filter_priority' => $filter_priority,
            'filter_queue' => $filter_queue,
            'filter_role' => $filter_role,
            'filter_suspended' => $filter_suspended,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $tasks_total = $this->model_tool_todo->getTasksTotal($filter_data);
     

        $results = $this->model_tool_todo->getTasks($filter_data);

       
        
        foreach ($results as $result) {
            $data['tasks'][] = array(
                'task_id' => $result['task_id'],
                'description' => $this->getDescription($result['description'], 256),
                'author' => $result['author_username'],
                'contractor' => $result['contractor_username'],
                'priority' => $data['text_priority_' . $result['priority']],
                'status' => $result['status'],
                'queue' => $this->model_tool_todo->getQueue($filter_role, $result['status']),
                'deadline' => $result['deadline'] ? date($this->language->get('date_format_short'), strtotime($result['deadline'])) : '',
                'edit' => $this->url->link('tool/todo/edit', 'token=' . $this->session->data['token'] . '&task_id=' . $result['task_id'] . $url, 'SSL'),
                'log' => $this->url->link('tool/todo/log', 'token=' . $this->session->data['token'] . '&task_id=' . $result['task_id'] . $url, 'SSL'),
                'editable' => $result['author_id'] == $this->user->getId() || ($result['status'] != 'closed' && $result['status'] != 'archived'),
            );
        }

        
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

        $url = $query_filter;

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_author'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&sort=a.username' . $url, 'SSL');
        $data['sort_contractor'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&sort=c.username' . $url, 'SSL');
        $data['sort_priority'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&sort=priority' . $url, 'SSL');
        $data['sort_deadline'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&sort=deadline' . $url, 'SSL');
        $data['sort_queue'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

        $url = $query_filter;

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $tasks_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = '';//sprintf($this->language->get('text_pagination'), ($tasks_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($tasks_total - $this->config->get('config_limit_admin'))) ? $tasks_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $tasks_total, ceil($tasks_total / $this->config->get('config_limit_admin')));

        $data['filter_priority'] = $filter_priority;
        $data['filter_queue'] = $filter_queue;
        $data['filter_role'] = $filter_role;
        $data['filter_suspended'] = $filter_suspended;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->load->view('tool/todo_list', $data));
    }

    public function add() {
        $this->load->language('tool/todo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/todo');
        $this->load->model('user/user');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $data = $this->request->post;
      
            $data['text_log_title_add1'] = $this->language->get('text_log_title_add1');
            $data['text_log_title_add2'] = $this->language->get('text_log_title_add2');
            $data['text_log_title_edit'] = $this->language->get('text_log_title_edit');
            $data['text_log_contractor'] = $this->language->get('text_log_contractor');
            $data['text_log_priority'] = $this->language->get('text_log_priority');
            $data['text_log_queue'] = $this->language->get('text_log_queue');
            $data['text_log_onhold'] = $this->language->get('text_log_onhold');
            $data['text_yes'] = $this->language->get('text_yes');
            $data['text_no'] = $this->language->get('text_no');
            $data['text_log_deadline'] = $this->language->get('text_log_deadline');
            $data['text_log_deadline_not_set'] = $this->language->get('text_log_deadline_not_set');
            $data['value_priority'] = $this->language->get('text_priority_' . $data['priority']);
            $data['role'] = $data['author_id'] == $this->user->getId() ? 'author' : 'contractor';

           // $data['status'] = '';
            if (empty($this->request->get['task_id'])) {
                $data['status'] = empty($data['contractor_id']) ? '"free"' : '"assigned"';
            }
           // debugPre($data);
          //  exit;
            if (!empty($data['status'])) {
                $data['value_queue'] = $this->model_tool_todo->getQueue($data['role'], $data['status']);
            }
            $data['description'] = $this->model_tool_todo->prepareDescription($data['role'], $data['status'], $data['description']);

         
            
            if (!empty($this->request->get['task_id'])) {
                $data['task_id'] = $this->request->get['task_id'];
                $this->model_tool_todo->editTask($data);
            } else {
                $this->model_tool_todo->addTask($data);
            }
            

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->getBackUrl();
            $this->redirect($this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->add();
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'tool/todo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->get['task_id']) ||
                ($this->request->post['author_id'] == $this->user->getId() && !empty($this->request->post['status']) && $this->request->post['status'] == 'replied') ||
                (!empty($this->request->post['contractor_id']) && $this->request->post['contractor_id'] != $this->request->post['author_id'] && $this->request->post['contractor_id'] == $this->user->getId())) {
            if ((utf8_strlen($this->request->post['description']) < 4)) {
                $this->error['description'] = $this->language->get('error_description');
            }
        }

        if (!isset($this->request->get['task_id']) && $this->request->post['deadline'] && date_create($this->request->post['deadline']) < date_create('today')) {
            $this->error['deadline'] = $this->language->get('error_deadline');
        }

        if (empty($this->request->post['priority'])) {
            $this->error['priority'] = $this->language->get('error_priority');
        }

        return !$this->error;
    }

    protected function getForm() {
        $is_edit = true;
        if (isset($this->request->get['uid'])) {
            $task_info = $this->model_tool_todo->getTaskByUniqId($this->request->get['uid']);
            $this->request->get['task_id'] = $this->request->get['uid'];
        } else if (isset($this->request->get['task_id'])) {
            $task_info = $this->model_tool_todo->getTask($this->request->get['task_id']);
        } else {
            $is_edit = false;
        }
    
        $data['is_edit'] = $is_edit;
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !$is_edit ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_logview_header'] = $this->language->get('text_logview_header');

        $data['text_action_inwork'] = $this->language->get('text_action_inwork');
        $data['text_action_ask'] = $this->language->get('text_action_ask');
        $data['text_action_complete'] = $this->language->get('text_action_complete');
        $data['text_action_archive'] = $this->language->get('text_action_archive');
        $data['text_action_close'] = $this->language->get('text_action_close');
//        $data['text_action_edit']      = $this->language->get('text_action_edit');

        $data['text_priority_1'] = $this->language->get('text_priority_1');
        $data['text_priority_2'] = $this->language->get('text_priority_2');
        $data['text_priority_3'] = $this->language->get('text_priority_3');
        $data['text_priority_4'] = $this->language->get('text_priority_4');

        $data['entry_author_id'] = $this->language->get('entry_author_id');
        $data['entry_contractor_id'] = $this->language->get('entry_contractor_id');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_deadline'] = $this->language->get('entry_deadline');
        $data['entry_suspended_form'] = $this->language->get('entry_suspended_form');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_queue'] = $this->language->get('entry_queue');
        $data['entry_action'] = $this->language->get('entry_action');

        $data['help_deadline'] = $this->language->get('help_deadline');
        $data['help_description'] = $this->language->get('help_description');
        $data['help_contractor_id_a'] = $this->language->get('help_contractor_id_a');
        $data['help_contractor_id_c'] = $this->language->get('help_contractor_id_c');
        $data['help_action_inwork_a'] = $this->language->get('help_action_inwork_a');
        $data['help_action_inwork_c'] = $this->language->get('help_action_inwork_c');
        $data['help_action_accept'] = $this->language->get('help_action_accept');
        $data['help_action_ask'] = $this->language->get('help_action_ask');
        $data['help_action_reply'] = $this->language->get('help_action_reply');
        $data['help_action_complete'] = $this->language->get('help_action_complete');
        $data['help_action_archive'] = $this->language->get('help_action_archive');
        $data['help_action_close'] = $this->language->get('help_action_close');
//        $data['help_action_edit']     = $this->language->get('help_action_edit');
        $data['help_suspended_form'] = $this->language->get('help_suspended_form');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = '';
        }

        if (isset($this->error['deadline'])) {
            $data['error_deadline'] = $this->error['deadline'];
        } else {
            $data['error_deadline'] = '';
        }

        if (isset($this->error['contractor_id'])) {
            $data['error_contractor_id'] = $this->error['contractor_id'];
        } else {
            $data['error_contractor_id'] = '';
        }

        if (isset($this->error['priority'])) {
            $data['error_priority'] = $this->error['priority'];
        } else {
            $data['error_priority'] = '';
        }

        $url = $this->getBackUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!$is_edit) {
            $data['action'] = $this->url->link('tool/todo/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('tool/todo/edit', 'token=' . $this->session->data['token'] . '&task_id=' . $this->request->get['task_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['token'] = $this->session->data['token'];

        if ($is_edit) {
            $data['author_id'] = $task_info['author_id'];
            $data['task_id'] = $this->request->get['task_id'];
            $data['log'] = $this->logDirection($task_info['description']);
            $data['status'] = $task_info['status'];
            $data['onhold'] = $task_info['onhold'];
        } else {
            $data['author_id'] = $this->user->getId();
        }

        $data['author'] = $this->model_user_user->getUser($data['author_id'])['username'];
        $data['is_author'] = $data['author_id'] == $this->user->getId();

        if ($is_edit) {
            $data['queue'] = $this->model_tool_todo->getQueue(
                    $data['is_author'] ? 'author' : 'contractor', $task_info['status']);
            $data['can_suspend'] = $data['is_author'] &&
                    $data['status'] != 'archived' &&
                    $data['status'] != 'closed';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['priority'])) {
            $data['priority'] = $this->request->post['priority'];
        } elseif (!empty($task_info)) {
            $data['priority'] = $task_info['priority'];
        } else {
            $data['priority'] = '3';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->post['contractor_id'])) {
            $data['contractor_id'] = $this->request->post['contractor_id'];
        } elseif (!empty($task_info)) {
            $data['contractor_id'] = $task_info['contractor_id'];
        } else {
            $data['contractor_id'] = $this->user->getId();
        }
        if (!empty($data['contractor_id'])) {
            $data['contractor'] = $this->model_user_user->getUser($data['contractor_id'])['username'];
        } else {
            $data['contractor'] = null;
        }

        if (isset($this->request->post['deadline'])) {
            $data['deadline'] = $this->request->post['deadline'];
        } elseif (!empty($task_info['deadline'])) {
            $data['deadline'] = date_format(date_create($task_info['deadline']), 'Y-m-d');
        } else {
            $data['deadline'] = '';
        }

        // Required description
        if (!$is_edit ||
                ($data['author_id'] != $this->user->getId() &&
                !empty($data['contractor_id']) &&
                $data['contractor_id'] == $this->user->getId())) {
            $data['is_description_required'] = true;
        } else {
            $data['is_description_required'] = false;
        }

        // Action checkbox
        if ($is_edit) {
            $data['status_action'] = array();

            if ($data['is_author']) {
                $data['status_action']['archived'] = array(
                    'help' => $data['help_action_archive'],
                    'text' => $data['text_action_archive'],
                );
                $data['status_action']['closed'] = array(
                    'help' => $data['help_action_close'],
                    'text' => $data['text_action_close'],
                );
                switch ($task_info['status']) {
                    case 'asked':
                    case 'completed':
                        $data['status_action']['replied'] = array(
                            'help' => $data['help_action_inwork_a'],
                            'text' => $data['text_action_inwork'],
                        );
                        break;
                    case 'archived':
                        $data['status_action']['replied'] = array(
                            'help' => $data['help_action_inwork_a'],
                            'text' => $data['text_action_inwork'],
                        );
                        unset($data['status_action']['archived']);
                        break;
                    case 'closed':
                        $data['status_action']['replied'] = array(
                            'help' => $data['help_action_inwork_a'],
                            'text' => $data['text_action_inwork'],
                        );
                        unset($data['status_action']['closed']);
                        break;
                }
            } else {
                switch ($task_info['status']) {
                    case 'free':
                        $data['status_action']['inprogress'] = array(
                            'help' => $data['help_action_accept'],
                            'text' => $data['text_action_inwork'],
                        );
                        $data['status_action']['asked'] = array(
                            'help' => $data['help_action_ask'],
                            'text' => $data['text_action_ask'],
                        );
                        break;
                    case 'assigned':
                    case 'inprogress':
                    case 'replied':
                        $data['status_action']['asked'] = array(
                            'help' => $data['help_action_ask'],
                            'text' => $data['text_action_ask'],
                        );
                        $data['status_action']['completed'] = array(
                            'help' => $data['help_action_complete'],
                            'text' => $data['text_action_complete'],
                        );
                        break;
                    case 'asked':
                        $data['status_action']['inprogress'] = array(
                            'help' => $data['help_action_inwork_c'],
                            'text' => $data['text_action_inwork'],
                        );
                        $data['status_action']['completed'] = array(
                            'help' => $data['help_action_complete'],
                            'text' => $data['text_action_complete'],
                        );
                        break;
                    case 'completed':
                        $data['status_action']['inprogress'] = array(
                            'help' => $data['help_action_inwork'],
                            'text' => $data['text_action_inwork'],
                        );
                        $data['status_action']['asked'] = array(
                            'help' => $data['help_action_ask'],
                            'text' => $data['text_action_ask'],
                        );
                        break;
                }
            }
        }

        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');
        
        $data['header'] = $this->getChild('common/header');
       
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->load->view('tool/todo_form', $data));
    }

    public function user_autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('user/user');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'username',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_user_user->getUsers($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'user_id' => $result['user_id'],
                    'name' => strip_tags(html_entity_decode($result['username'], ENT_QUOTES, 'UTF-8'))
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

    /**
     * Returns decription of the last change, trimmed by "length" letters.
     * WARNING! Function is sensitive to structure of the "todo_log_*.tpl"
     * @param string $html_descr - text of a log
     * @param int $len - max length jf a deascription to display
     */
    protected function getDescription($html_descr, $len = null) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML('<?xml encoding="utf-8" ?><!DOCTYPE html>' . $html_descr);
        $title = $this->trimString($doc->getElementById('log-title')->nodeValue, $len);
        $node_list = $doc->getElementsByTagName('div');
        if ($node_list->length < 3) {
            $descr = $html_descr;
        } else {
            $descr = '';
            for ($i = 0; $i < $node_list->length; $i++) {
                $el = $node_list->item($i);
                if ($el->hasAttribute('data-log-description') && $descr = $el->nodeValue) {
                    $descr = $this->trimString($descr, $len);
                    break;
                }
            }
        }

        return '<div>' . $title . '</div>' . (!empty($descr) ? '<div>...</div>' . $descr : '');
    }

    /**
     * Changes log direction according to selected mode in the module cofiguration.
     * Log is stored in the descending order. Ascending order is created by moving nodes.
     * @param string $log - HTML log
     * @return string a log in the right direction
     */
    protected function logDirection($log) {
        if ($this->config->get('model_todo_log_direction') == 'desc') {
            $result = $log;
        } else {
            $result = '';
            $doc = new DOMDocument('1.0', 'UTF-8');
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $log);
            $node_list = $doc->getElementsByTagName('div');
            for ($i = 0; $i < $node_list->length; $i++) {
                $el = $node_list->item($i);
                if ($el->getAttribute('class') == 'todo-log') {
                    $result = $doc->saveHTML($el) . $result;
                }
            }
        }
        return $result;
    }

    public function log() {
        if (isset($this->request->get['task_id'])) {
            $data = array();
            $this->load->language('tool/todo');
            $this->load->model('tool/todo');

            $data['log'] = $this->logDirection($this->model_tool_todo->getTask($this->request->get['task_id'])['description']);
            $data['text_logview_header'] = $this->language->get('text_logview_header');

            $this->response->addHeader('Content-Type: text/html');
            $this->response->setOutput($this->load->view('tool/todo_log', $data));
        }
    }

    public function complete() {
        $this->load->language('tool/todo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/todo');
        $this->load->model('user/user');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $data = $this->request->post;
            $data['text_log_title_edit'] = $this->language->get('text_log_title_edit');
            $data['text_log_status'] = $this->language->get('text_log_status');
            $data['text_autocomplete_description'] = $this->language->get('text_autocomplete_description');

            $this->model_tool_todo->completeTasks($data);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $url = $this->getBackUrl();

        $this->redirect($this->url->link('tool/todo', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    protected function getBackUrl() {
        $url = '';

        if (isset($this->request->get['filter_priority'])) {
            $url .= '&filter_priority=' . $this->request->get['filter_priority'];
        }

        if (isset($this->request->get['filter_queue'])) {
            $url .= '&filter_queue=' . $this->request->get['filter_queue'];
        }

        if (isset($this->request->get['filter_role'])) {
            $url .= '&filter_role=' . $this->request->get['filter_role'];
        }

        if (isset($this->request->get['filter_suspended'])) {
            $url .= '&filter_suspended=' . $this->request->get['filter_suspended'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
    }

    protected function trimString($str, $len = null) {
        if (!empty($len) && $len > 3 && mb_strlen($str) > $len) {
            $result = mb_substr($str, 0, $len - 3) . '...';
        } else {
            $result = $str;
        }
        return $result;
    }

    public function queue() {
        if (isset($this->request->get['filter_role'])) {
            $this->load->model('tool/todo');
            $options = $this->model_tool_todo->getQueues($this->request->get['filter_role']);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($options));
        }
    }

}
