<?php

class ModelAccountActivity extends \Core\Model {

    public function addActivity($key, $data) {
        if (isset($data['customer_id'])) {
            $customer_id = $data['customer_id'];
        } else {
            $customer_id = 0;
        }

        $this->db->query("INSERT INTO `#__customer_activity` SET `customer_id` = '" . (int) $customer_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(serialize($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
    }

}
