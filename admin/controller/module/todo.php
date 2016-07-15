<?php

class ControllerModuleTodo extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/todo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_todo', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_asc'] = $this->language->get('text_asc');
        $data['text_desc'] = $this->language->get('text_desc');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_autofilter'] = $this->language->get('entry_autofilter');
        $data['entry_email_notification'] = $this->language->get('entry_email_notification');
        $data['entry_log_direction'] = $this->language->get('entry_log_direction');

        $data['help_autofilter'] = $this->language->get('help_autofilter');
        $data['help_email_notification'] = $this->language->get('help_email_notification');
        $data['help_log_direction'] = $this->language->get('help_log_direction');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/todo', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('module/todo', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['module_todo_autofilter'])) {
            $data['module_todo_autofilter'] = $this->request->post['module_todo_autofilter'];
        } else {
            $data['module_todo_autofilter'] = $this->config->get('module_todo_autofilter');
        }
        if (!isset($data['module_todo_autofilter'])) {
            $data['module_todo_autofilter'] = 1;
        }

        if (isset($this->request->post['module_todo_email_notification'])) {
            $data['module_todo_email_notification'] = $this->request->post['module_todo_email_notification'];
        } else {
            $data['module_todo_email_notification'] = $this->config->get('module_todo_email_notification');
        }
        if (!isset($data['module_todo_email_notification'])) {
            $data['module_todo_email_notification'] = 1;
        }

        if (isset($this->request->post['module_todo_log_direction'])) {
            $data['module_todo_log_direction'] = $this->request->post['module_todo_log_direction'];
        } else {
            $data['module_todo_log_direction'] = $this->config->get('module_todo_log_direction');
        }
        if (!isset($data['module_todo_log_direction'])) {
            $data['module_todo_log_direction'] = 'desc';
        }

        if (isset($this->request->post['module_todo_status'])) {
            $data['module_todo_status'] = $this->request->post['module_todo_status'];
        } else {
            $data['module_todo_status'] = $this->config->get('module_todo_status');
        }

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->load->view('module/todo', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/todo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `#__mod_task`");
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('module_todo');
    }

    public function install() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'tool/todo');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'tool/todo');

        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('module_todo', 'admin.module.menu', 'module/todo/menu');
        $this->model_extension_event->addEvent('module_todo', 'admin.header.navs', 'module/todo/header');

        $this->db->query("CREATE TABLE IF NOT EXISTS `#__mod_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `contractor_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '3',
  `status` enum('free','assigned','inprogress','asked','replied','completed','archived','closed') NOT NULL DEFAULT 'free',
  `deadline` datetime DEFAULT NULL,
  `onhold` tinyint NOT NULL DEFAULT false,
  `uniqid` char(23) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  KEY `task_autor_id` (`author_id`),
  KEY `task_status` (`status`),
  KEY `task_onhold` (`onhold`),
  UNIQUE KEY `task_uniqid` (`uniqid`),
  KEY `task_date_created` (`date_created`),
  KEY `task_date_modified` (`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    }

    public function menu(&$menu) {
        if ($this->config->get('module_todo_status')) {
            $this->load->language('module/todo');
            $menu['extensions']['children']['module_todo'] = array(
                'order' => '3',
                'label' => $this->language->get('button_todo'),
                'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'], 'SSL')
            );
        }
    }

    public function header(&$headernav) {

        if ($this->config->get('module_todo_status')) {
            $this->load->language('module/todo');
            $this->load->model('tool/todo');
            $todo_awaiting_decision_total = $this->model_tool_todo->getAwaitingDecisionTotal();
            $todo_myqueue_total = $this->model_tool_todo->getMyQueueTotal();
            $todo_not_distributed_total = $this->model_tool_todo->getNotDistributedTotal();
            $total = $todo_awaiting_decision_total + $todo_myqueue_total + $todo_not_distributed_total;
            //TODO
            $alerts = array();
            $alerts['awaiting'] = array(
                'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&filter_queue=awaiting_decision&filter_role=author', 'SSL'),
                'text' => $this->language->get('text_awaiting_decision'),
                'class' => ($todo_awaiting_decision_total) ? 'danger' : 'success',
                'total' => $todo_awaiting_decision_total
            );
            $alerts['myque'] = array(
                'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&filter_queue=myqueue&filter_role=contractor', 'SSL'),
                'text' => $this->language->get('text_myqueue'),
                'class' => ($todo_myqueue_total) ? 'danger' : 'success',
                'total' => $todo_myqueue_total
            );
            $alerts['not_dist'] = array(
                'href' => $this->url->link('tool/todo', 'token=' . $this->session->data['token'] . '&filter_queue=not_distributed&filter_role=contractor', 'SSL'),
                'text' => $this->language->get('text_not_distributed'),
                'class' => ($todo_not_distributed_total) ? 'danger' : 'success',
                'total' => $todo_not_distributed_total
            );
            $headernav['todo'] = array(
                'title' => $this->language->get('text_todo'),
                'icon' => 'fa fa-tasks',
                'class' => ($total)?'danger' : 'success',
                'order' => 0,
                'total' => $total,
                'items' => $alerts
            );
        }
    }

}
