<?php

class ModelSaleCustomer extends \Core\Model {

    public function addCustomer($data) {
        if (!is_array($data['customer_group_id'])) {
            $data['customer_group_id'] = array($data['customer_group_id']);
        }
        $this->db->query("INSERT INTO #__customer SET customer_group_id = '" . json_encode($data['customer_group_id']) . "', "
                . " firstname = '" . $this->db->escape($data['firstname']) . "', "
                . " lastname = '" . $this->db->escape($data['lastname']) . "', "
                . " email = '" . $this->db->escape($data['email']) . "', "
                . " telephone = '" . $this->db->escape($data['telephone']) . "', "
                . " newsletter = '" . (int) $data['newsletter'] . "', "
                . " salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', "
                . " password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', "
                . " status = '" . (int) $data['status'] . "', "
                . " approved = '" . (int) $data['approved'] . "', "
                . " country_id = '" . (int) $data['country_id'] . "', "
                . " custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', "
                . " date_added = NOW()");

        $customer_id = $this->db->getLastId();

        if (!empty($data['profile_img'])) {
            $this->updateProfileImage($customer_id, $data['profile_img']);
        }

        return $customer_id;
    }

    public function editCustomer($customer_id, $data) {
        if (!is_array($data['customer_group_id'])) {
            $data['customer_group_id'] = array($data['customer_group_id']);
        }

        $this->db->query("UPDATE #__customer SET customer_group_id = '" . json_encode($data['customer_group_id']) . "', "
                . " firstname = '" . $this->db->escape($data['firstname']) . "', "
                . " lastname = '" . $this->db->escape($data['lastname']) . "', "
                . " email = '" . $this->db->escape($data['email']) . "', "
                . " telephone = '" . $this->db->escape($data['telephone']) . "', "
                . " newsletter = '" . (int) $data['newsletter'] . "', status = '" . (int) $data['status'] . "', "
                . " approved = '" . (int) $data['approved'] . "', "
                . " custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', "
                . " country_id = '" . (int) $data['country_id'] . "' WHERE customer_id = '" . (int) $customer_id . "'");

        if ($data['password']) {
            $this->db->query("UPDATE #__customer SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE customer_id = '" . (int) $customer_id . "'");
        }
        if (!empty($data['profile_img'])) {
            $this->updateProfileImage($customer_id, $data['profile_img']);
        }
    }

    public function updateProfileImage($customer_id, $profile_img) {
        $this->db->query("UPDATE #__customer SET profile_img = '" . $this->db->escape($profile_img) . "' WHERE customer_id = '" . (int) $customer_id . "'");
    }

    public function editToken($customer_id, $token) {
        $this->db->query("UPDATE #__customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int) $customer_id . "'");
    }

    public function deleteCustomer($customer_id) {
        $this->db->query("DELETE FROM #__customer WHERE customer_id = '" . (int) $customer_id . "'");
        $this->db->query("DELETE FROM #__customer_history WHERE customer_id = '" . (int) $customer_id . "'");
        $this->db->query("DELETE FROM #__customer_ip WHERE customer_id = '" . (int) $customer_id . "'");
    }

    public function getCustomer($customer_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__customer WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row;
    }

    public function getCustomerByEmail($email) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function getCustomers($data = array()) {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM #__customer c  WHERE 1";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== null) {
            $implode[] = "c.newsletter = '" . (int) $data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id like '%\"" . (int) $data['filter_customer_group_id'] . "\"%'";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM #__customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== null) {
            $implode[] = "c.status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && $data['filter_approved'] !== null) {
            $implode[] = "c.approved = '" . (int) $data['filter_approved'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'c.email',
            'c.status',
            'c.approved',
            'c.ip',
            'c.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        foreach ($query->rows as &$row) {
            $row['customer_group_id'] = json_decode($row['customer_group_id'], 1);

            $customer_groups = array();
            foreach ($row['customer_group_id'] as $cgid) {
                $cq = $this->db->query("select name from #__customer_group where customer_group_id = '" . (int) $cgid . "'")->row;
                if (!empty($cq['name'])) {
                    $customer_groups[] = $cq['name'];
                }
            }
            $row['customer_group'] = implode(", ", $customer_groups);
        }

        return $query->rows;
    }

    public function approve($customer_id) {
        $customer_info = $this->getCustomer($customer_id);

        if ($customer_info) {
            $this->db->query("UPDATE #__customer SET approved = '1' WHERE customer_id = '" . (int) $customer_id . "'");

            $this->load->language('mail/customer');



            $store_name = $this->config->get('config_name');
            $store_url = HTTP_CATALOG . 'index.php?p=account/login';


            $message = sprintf($this->language->get('text_approve_welcome'), $store_name) . "\n\n";
            $message .= $this->language->get('text_approve_login') . "\n";
            $message .= $store_url . "\n\n";
            $message .= $this->language->get('text_approve_services') . "\n\n";
            $message .= $this->language->get('text_approve_thanks') . "\n";
            $message .= $store_name;

            $mail = new \Core\Mail();
            $mail->tags = array('Customer Approve');

            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->username = $this->config->get('config_mail_smtp_username');
            $mail->password = $this->config->get('config_mail_smtp_password');
            $mail->port = $this->config->get('config_mail_smtp_port');
            $mail->timeout = $this->config->get('config_mail_smtp_timeout');
            $mail->setTo($customer_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($store_name);
            $mail->setSubject(sprintf($this->language->get('text_approve_subject'), $store_name));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
        }
    }

    public function getTotalCustomers($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM #__customer";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== null) {
            $implode[] = "newsletter = '" . (int) $data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "customer_group_id like '%\"" . (int) $data['filter_customer_group_id'] . "\"%'";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "customer_id IN (SELECT customer_id FROM #__customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== null) {
            $implode[] = "status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && $data['filter_approved'] !== null) {
            $implode[] = "approved = '" . (int) $data['filter_approved'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalCustomersAwaitingApproval() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer WHERE status = '0' OR approved = '0'");

        return $query->row['total'];
    }

    public function getTotalCustomersByCustomerGroupId($customer_group_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer WHERE customer_group_id = '" . (int) $customer_group_id . "'");

        return $query->row['total'];
    }

    public function addHistory($customer_id, $comment, $notify) {
        $this->db->query("INSERT INTO #__customer_history SET customer_id = '" . (int) $customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', notified = '" . (int) $notify . "', date_added = NOW()");
        if ($notify) {
            $mail = new \Core\Mail();
            $mail->tags = array('Comment Notification');
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->username = $this->config->get('config_mail_smtp_username');
            $mail->password = $this->config->get('config_mail_smtp_password');
            $mail->port = $this->config->get('config_mail_smtp_port');
            $mail->timeout = $this->config->get('config_mail_smtp_timeout');

            $customer_info = $this->getCustomer($customer_id);
            $this->load->language('mail/history');
            $mail->setTo($customer_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(sprintf($this->language->get('subject_comment_notification'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
            $message = sprintf($this->language->get('text_comment_notification'), $this->config->get('config_name')) . "\n\n";
            $message .= sprintf($this->language->get('text_comment_comment'), "\n" . $comment);
            $mail->setText($message);


            try {
                $mail->send();
            } catch (Exception $e) {
                
            }
        }
    }

    public function getHistories($customer_id, $start = 0, $limit = 10) {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query("SELECT comment,notified, date_added FROM #__customer_history WHERE customer_id = '" . (int) $customer_id . "' ORDER BY date_added DESC LIMIT " . (int) $start . "," . (int) $limit);

        return $query->rows;
    }

    public function getTotalHistories($customer_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_history WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row['total'];
    }

    public function getIps($customer_id) {
        $query = $this->db->query("SELECT * FROM #__customer_ip WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->rows;
    }

    public function getTotalIps($customer_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_ip WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row['total'];
    }

    public function getTotalCustomersByIp($ip) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_ip WHERE ip = '" . $this->db->escape($ip) . "'");

        return $query->row['total'];
    }

    public function addBanIp($ip) {
        $this->db->query("INSERT INTO `#__customer_ban_ip` SET `ip` = '" . $this->db->escape($ip) . "'");
    }

    public function removeBanIp($ip) {
        $this->db->query("DELETE FROM `#__customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
    }

    public function getTotalBanIpsByIp($ip) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `#__customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");

        return $query->row['total'];
    }

    public function getTotalLoginAttempts($email) {
        $query = $this->db->query("SELECT * FROM `#__customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

        return $query->row;
    }

    public function deleteLoginAttempts($email) {
        $this->db->query("DELETE FROM `#__customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
    }

    public function addReward($customer_id, $description = '', $points = '') {
        $customer_info = $this->getCustomer($customer_id);

        if ($customer_info) {
            $this->db->query("INSERT INTO #__customer_reward SET customer_id = '" . (int) $customer_id . "', points = '" . (int) $points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

            $this->load->language('mail/customer');

            $store_name = $this->config->get('config_name');



            $message = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
            $message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));


            $mail = new \Core\Mail();
            $mail->tags = array('Points Notification');
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');



            $mail->setTo($customer_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(sprintf($this->language->get('text_reward_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
            $mail->setText($message);

            try {
                $mail->send();
            } catch (Exception $e) {
                
            }
        }
    }

    public function deleteReward($order_id) {
        $this->db->query("DELETE FROM #__customer_reward WHERE order_id = '" . (int) $order_id . "' AND points > 0");
    }

    public function getRewards($customer_id, $start = 0, $limit = 10) {
        $query = $this->db->query("SELECT * FROM #__customer_reward WHERE customer_id = '" . (int) $customer_id . "' ORDER BY date_added DESC LIMIT " . (int) $start . "," . (int) $limit);

        return $query->rows;
    }

    public function getTotalRewards($customer_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_reward WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row['total'];
    }

    public function getRewardTotal($customer_id) {
        $query = $this->db->query("SELECT SUM(points) AS total FROM #__customer_reward WHERE customer_id = '" . (int) $customer_id . "'");

        return $query->row['total'];
    }

}
