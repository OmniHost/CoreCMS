<?php

################################################################################################
# ToDo-CRM Model                                                          	
# Copyright (c) 2014 Sergey Ogarkov <sogarkov@gmail.com>
# GNU GPL v.3
################################################################################################

class ModelToolTodo extends \Core\Model {

    protected $queue_status_map = array(
        'author' => array(
            'awaiting_decision' => array('asked', 'completed'),
            'inwork' => array('assigned', 'inprogress', 'replied'),
            'not_distributed' => array('free'),
            'finished' => array('archived', 'closed'),
        ),
        'contractor' => array(
            'myqueue' => array('assigned', 'replied'),
            'inwork' => array('inprogress'),
            'not_distributed' => array('free'),
            'awaiting_answer' => array('asked'),
            'finished' => array('completed', 'archived', 'closed'),
        ),
    );

    public function getAwaitingDecisionTotal() {
        $sql = '
SELECT 
    count(*) as count
FROM #__mod_task AS t
WHERE
    t.author_id = ' . $this->user->getId() . '
    AND t.onhold = 0
    AND t.status IN("' . implode('","', $this->queue_status_map['author']['awaiting_decision']) . '")';

        $query = $this->db->query($sql);
        return $query->row['count'];
    }

    public function getMyQueueTotal() {
        $sql = '
SELECT 
    count(*) as count
FROM #__mod_task AS t
WHERE
    t.contractor_id = ' . $this->user->getId() . '
    AND t.onhold = 0
    AND t.status IN("' . implode('","', $this->queue_status_map['contractor']['myqueue']) . '")';

        $query = $this->db->query($sql);
        return $query->row['count'];
    }

    public function getNotDistributedTotal() {
        $sql = '
SELECT
    count(*) as count
FROM #__mod_task AS t
WHERE
    t.onhold = 0
    AND t.status IN("' . implode('","', $this->queue_status_map['author']['not_distributed']) . '")';

        $query = $this->db->query($sql);
        return $query->row['count'];
    }

    public function getTasksTotal($data) {
        $where = 'WHERE 1';
        if (!empty($data['filter_priority'])) {
            $where .= ' AND t.priority = "' . $data['filter_priority'] . '"';
        }
        if (!empty($data['filter_queue'])) {
            $where .= ' AND t.status IN ("' . implode('","', $this->queue_status_map[$data['filter_role']][$data['filter_queue']]) . '")';
        }
        if ($data['filter_role'] == 'author' && !empty($data['filter_suspended'])) {
            $where .= ' AND t.onhold = 1';
        } else {
            $where .= ' AND t.onhold = 0';
        }
        if ($data['filter_role'] == 'author') {
            $where .= ' AND t.author_id = ' . $this->user->getId();
        } else {
            $where .= ' AND (t.contractor_id = ' . $this->user->getId() . ' OR t.contractor_id IS NULL)';
        }

        $sql = '
SELECT 
    count(*) as count
FROM #__mod_task AS t ' . $where;

        $query = $this->db->query($sql);
        return $query->row['count'];
    }

    public function getTasks($data) {
        $where = 'WHERE 1';
        if (!empty($data['filter_priority'])) {
            $where .= ' AND t.priority = "' . $data['filter_priority'] . '"';
        }
        if (!empty($data['filter_queue'])) {
            $where .= ' AND t.status IN ("' . implode('","', $this->queue_status_map[$data['filter_role']][$data['filter_queue']]) . '")';
        }
        if ($data['filter_role'] == 'author' && !empty($data['filter_suspended'])) {
            $where .= ' AND t.onhold = 1';
        } else {
            $where .= ' AND t.onhold = 0';
        }
        if ($data['filter_role'] == 'author') {
            $where .= ' AND t.author_id = ' . $this->user->getId();
        } else {
            $where .= ' AND (t.contractor_id = ' . $this->user->getId() . ' OR t.contractor_id IS NULL)';
        }

        $order = '';
        if (!empty($data['sort'])) {
            $order .= ' ORDER BY ' . $data['sort'];
            if (!empty($data['order'])) {
                $order .= ' ' . $data['order'];
            }
        }
        if ($order == '') {
            $order = ' ORDER BY closed ASC';
        }
        $order .= ', t.date_modified DESC';

        $limit = '';
        if (!empty($data['start'])) {
            $limit .= ' LIMIT ' . $data['start'];
            if (!empty($data['limit'])) {
                $limit .= ', ' . $data['limit'];
            }
        }

        $sql = '
SELECT 
    t.task_id, t.description, t.priority, t.status, 
    t.deadline, t.date_created, t.date_modified, 
    t.author_id, t.contractor_id,
    a.username AS author_username, a.firstname AS author_firstname, a.lastname AS author_lastname,
    c.username AS contractor_username, c.firstname AS contractor_firstname, c.lastname AS contractor_lastname,
    IF(t.status="closed" OR t.status="archived",1,0) AS closed
FROM #__mod_task AS t
    INNER JOIN #__user AS a ON a.user_id = t.author_id
    LEFT JOIN #__user AS c ON c.user_id = t.contractor_id ' . $where . $order . $limit;

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTask($task_id) {
        $sql = "SELECT * FROM #__mod_task WHERE task_id=" . (int) $task_id;
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getTaskByUniqId($uniqid) {
        $sql = 'SELECT * FROM #__mod_task WHERE uniqid="' . $uniqid . '"';
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function addTask($data) {
        $data['date_created'] = date('Y-m-d H:i');
        $description = $this->load->view('tool/todo_log_create', $data);

        $hash = uniqid('', true);
        $sql = 'INSERT INTO #__mod_task SET
            author_id = ' . $data['author_id'] . ',
            contractor_id = ' . (empty($data['contractor_id']) ? 'NULL' : $data['contractor_id']) . ',
            description = "' . $this->db->escape($description) . '",
            priority = ' . $data['priority'] . ',
            status = ' . (empty($data['contractor_id']) ? '"free"' : '"assigned"') . ',' .
                ($data['deadline'] ? 'deadline = "' . $data['deadline'] . '",' : '') . '
            uniqid = "' . $hash . '",
            date_created = NOW(),
            date_modified = NOW()';
        $this->db->query($sql);
        $this->emailNotification($data['author_id'], $data['contractor_id'], $description, $hash);
    }

    public function editTask($data) {
        $old = $this->getTask($data['task_id']);
        $this->setNewState($data, $old);

        if (!$this->isEqual($data, $old)) {

            $changed = array();
            $changed['date_modified'] = date('Y-m-d H:i');
            $changed['description'] = $data['description'];
            $changed['username'] = $this->user->getUserName();
            $changed['data'] = $data;

            if ($data['contractor_id'] != $old['contractor_id']) {
                $changed['contractor'] = $data['contractor'];
            }
            if ($data['priority'] != $old['priority']) {
                $changed['value_priority'] = $data['value_priority'];
            }
            if (!empty($data['status']) && ($data['status'] != $old['status'])) {
                $changed['value_queue'] = $this->getQueue($data['role'], $data['status']);
            }

            if (date_format(date_create($data['deadline']), 'Y-m-d') != date_format(date_create($old['deadline']), 'Y-m-d')) {
                $changed['deadline'] = $data['deadline'];
            }
            if (isset($data['onhold']) && $data['onhold'] != $old['onhold']) {
                $changed['value_onhold'] = $data['onhold'] ? $data['text_yes'] : $data['text_no'];
            }

//HERE
            $log = $this->load->view('tool/todo_log_edit', $changed);

            $description = $log . $old['description'];
            $sql = 'UPDATE #__mod_task SET
                contractor_id = ' . (empty($data['contractor_id']) ? 'NULL' : $data['contractor_id']) . ',
                description = "' . $this->db->escape($description) . '",
                priority = ' . $data['priority'] . ',' .
                    (isset($data['onhold']) && $data['onhold'] != $old['onhold'] ? 'onhold =' . $data['onhold'] . ',' : '') .
                    (empty($data['status']) ? '' : 'status = "' . $data['status'] . '",') . '
                deadline = ' . (empty($data['deadline']) ? 'NULL' : '"' . $data['deadline'] . '"') . ',
                date_modified = NOW()
            WHERE task_id = ' . $data['task_id'];
            
    

            $this->db->query($sql);
            $this->emailNotification($data['author_id'], $data['contractor_id'], $log, $old['uniqid']);
        }
    }

    public function completeTasks($data) {
        $changed = array();
        $changed['date_modified'] = date('Y-m-d H:i');
        $changed['username'] = $this->user->getUserName();
        $changed['description'] = $data['text_autocomplete_description'];
        $changed['data'] = $data;
        $sql = 'SELECT task_id, author_id, contractor_id, status, description
            FROM #__mod_task
            WHERE task_id IN (' . implode(',', $data['selected']) . ')';
        $tasks = $this->db->query($sql)->rows;
        foreach ($tasks as $task) {
            if ($task['author_id'] == $this->user->getId()) {
                $new_status = 'closed';
            } else {
                $new_status = 'completed';
            }

            $changed['value_status'] = $this->language->get('text_status_' . $new_status);
            $description = $this->load->view('tool/todo_log_complete', $changed) . $task['description'];

            if (($task['author_id'] == $this->user->getId() && $task['status'] != "closed") || ($task['contractor_id'] == $this->user->getId() &&
                    ($task['status'] == "new" || $task['status'] == "inprogress" || $task['status'] == "hold"))) {

                $sql = 'UPDATE #__mod_task SET 
                    status = "' . $new_status . '",
                    description = "' . $this->db->escape($description) . '",
                    date_modified = NOW()
                    WHERE task_id=' . $task['task_id'];
                $this->db->query($sql);
            }
        }
    }

    public function getQueues($role) {
        $result = array();
        $this->load->language('rbose/todo');
        foreach ($this->queue_status_map[$role] as $key => $val) {
            $result[] = array(
                'value' => $key,
                'name' => $this->language->get('text_filter_queue_' . $key),
            );
        }
        return $result;
    }

    public function getQueue($role, $status) {
        $result = '';
        $this->load->language('rbose/todo');
        foreach ($this->queue_status_map[$role] as $key => $val) {
            if (in_array($status, $val)) {
                $result = $this->language->get('text_filter_queue_' . $key);
                break;
            }
        }
    
        return $result;
    }

    public function prepareDescription($role, $status, $description) {
        if ($role == 'author' && empty($description) &&
                ($status == 'free' || $status == 'assigned' || $status == 'archived' || $status == 'closed')) {
            $this->load->language('rbose/todo');
            $description = $this->language->get('text_description_default_' . $status);
        }
        return $description;
    }

    protected function isEqual($new, $old) {
        $different = !empty($new['description']) ||
                $new['contractor_id'] != $old['contractor_id'] ||
                $new['priority'] != $old['priority'] ||
                date_format(date_create($new['deadline']), 'Y-m-d') != date_format(date_create($old['deadline']), 'Y-m-d') ||
                (isset($new['onhold']) && $new['onhold'] != $old['onhold']) ||
                (isset($new['status']) && $new['status'] != $old['status'])
        ;
        return !$different;
    }

    protected function setNewState(&$new, $old) {

        if ($new['author_id'] == $this->user->getId()) {
            if ($new['status'] != $old['status'] && ($new['status'] == 'archived' || $new['status'] == 'closed')) {
                // nothing to do
            } else if (!empty($old['contractor_id']) && empty($new['contractor_id'])) {
                $new['status'] = 'free';
            } else if (!empty($new['contractor_id']) && ($old['contractor_id'] != $new['contractor_id'])) {
                $new['status'] = 'assigned';
            } else if (($old['status'] == 'asked' || $old['status'] == 'completed' || $old['status'] == 'archived' || $old['status'] == 'closed') && $new['status'] == 'replied') {
                $new['status'] = 'replied';
            } else {
                $new['status'] = $old['status'];
            }
        } else {
            if ($old['status'] == 'free') {
                if (!empty($new['contractor_id']) && $new['contractor_id'] != $this->user->getId()) {
                    $new['status'] = 'assigned';
                } else if (!empty($new['contractor_id']) && $new['contractor_id'] == $this->user->getId()) {
                    $new['status'] = 'inprogress';
                } else if ($new['status'] == 'asked' || $new['status'] == 'inprogress') {
                    $new['contractor_id'] = $this->user->getId();
                } else {
                    $new['status'] = $old['status'];
                }
            } else if (empty($new['contractor_id'])) {
                $new['status'] = 'free';
            } else if ($old['contractor_id'] != $new['contractor_id']) {
                $new['status'] = 'assigned';
            } else if (!empty($new['status'])) {
                // do nothing
            } else if ($old['status'] == 'assigned' || $old['status'] == 'replied') {
                $new['status'] = 'inprogress';
            } else {
                $new['status'] = $old['status'];
            }
        }
    }

    public function emailNotification($author_id, $contractor_id, $description, $hash) {
        // No need to notify himself
        if ($author_id == $contractor_id && $author_id == $this->user->getId())
            return;

        if ($this->config->get('module_todo_email_notification')) {
            // Get recipients. They can be both author and contractor 
            // if current contractor delegates a task to other one.
            $to = array();
            if ($author_id != $this->user->getId()) {
                $to[] = $author_id;
            }
            if (!empty($contractor_id) && $contractor_id != $this->user->getId()) {
                $to[] = $contractor_id;
            }

            $this->load->language('tool/todo');
            $this->load->model('user/user');

            $data = array();
            $data['text_email_subject'] = $this->language->get('text_email_subject');
            $data['text_email_title'] = $this->language->get('text_email_title');
            $data['text_email_content'] = $this->language->get('text_email_content');
            $data['text_email_signature'] = $this->language->get('text_email_signature');

            foreach ($to as $recipient_id) {
                $user = $this->model_user_user->getUser($recipient_id);
                $data['firstname'] = $user['firstname'];
                $data['lastname'] = $user['lastname'];
                $data['shopname'] = $this->config->get('config_name');
                $data['description'] = $this->logToEmail($description);
                $data['url'] = $this->url->link('tool/todo/edit', 'uid=' . $hash, 'SSL');

                $message = $this->load->view('tool/todo_email_notification.phtml', $data);

                if (preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $user['email'])) {
                    $mail = new \Core\Mail();
                    $mail->tags = array('todo notification');
                    $mail->protocol = $this->config->get('config_mail_protocol');
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->hostname = $this->config->get('config_smtp_host');
                    $mail->username = $this->config->get('config_smtp_username');
                    $mail->password = $this->config->get('config_smtp_password');
                    $mail->port = $this->config->get('config_smtp_port');
                    $mail->timeout = $this->config->get('config_smtp_timeout');
                    $mail->setTo('"' . $user['firstname'] . ' ' . $user['lastname'] . '" <' . $user['email'] . '>');
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender($data['shopname']);
                    $mail->setSubject($data['text_email_subject']);
                    $mail->setHtml($message);

                    $mail->send();
                }
            }
        }
    }

    protected function logToEmail($description) {
        $description = str_ireplace('class="todo-log"', 'style="border-style:solid;border-width:1px;border-color:#aaaaaa;padding:4px;"', $description);
        $description = str_ireplace('class="title"', 'style="font-style:italic;font-weight:bold;"', $description);
        $description = str_ireplace('class="params"', 'style="font-style:italic;"', $description);
        return $description;
    }

}
