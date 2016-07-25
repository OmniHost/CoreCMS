<?php

class ModelLocalisationOrderStatus extends \Core\Model {

    public function addOrderStatus($data) {

        $this->db->query("INSERT INTO #__order_status SET  name = '" . $this->db->escape($data['name']) . "'");

        $order_status_id = $this->db->getLastId();


        $this->cache->delete('order_status');

        return $order_status_id;
    }

    public function editOrderStatus($order_status_id, $data) {
        $this->db->query("update #__order_status SET name = '" . $this->db->escape($data['name']) . "' WHERE order_status_id = '" . (int) $order_status_id . "'");

        $this->cache->delete('order_status');
    }

    public function deleteOrderStatus($order_status_id) {
        $this->db->query("DELETE FROM #__order_status WHERE order_status_id = '" . (int) $order_status_id . "'");

        $this->cache->delete('order_status');
    }

    public function getOrderStatus($order_status_id) {
        $query = $this->db->query("SELECT * FROM #__order_status WHERE order_status_id = '" . (int) $order_status_id . "' ");

        return $query->row;
    }

    public function getOrderStatuses($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM #__order_status ";

            $sql .= " ORDER BY name";

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

            return $query->rows;
        } else {
            $order_status_data = $this->cache->get('order_status');

            if (!$order_status_data) {
                $query = $this->db->query("SELECT order_status_id, name FROM #__order_status ORDER BY name");

                $order_status_data = $query->rows;

                $this->cache->set('order_status', $order_status_data);
            }

            return $order_status_data;
        }
    }

    public function getTotalOrderStatuses() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__order_status ");

        return $query->row['total'];
    }

}
