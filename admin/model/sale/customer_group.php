<?php

class ModelSaleCustomerGroup extends \Core\Model {

    public function addCustomerGroup($data) {
        $this->db->query("INSERT INTO #__customer_group SET approval = '" . (int) $data['approval'] . "', sort_order = '" . (int) $data['sort_order'] . "', name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "'");
        return $this->db->getLastId();
    }

    public function editCustomerGroup($customer_group_id, $data) {
        $this->db->query("UPDATE #__customer_group SET approval = '" . (int) $data['approval'] . "', sort_order = '" . (int) $data['sort_order'] . "', name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "' WHERE customer_group_id = '" . (int) $customer_group_id . "'");

        
    }

    public function deleteCustomerGroup($customer_group_id) {
        $this->db->query("DELETE FROM #__customer_group WHERE customer_group_id = '" . (int) $customer_group_id . "'");

        $this->db->query("DELETE FROM #__allowed_groups WHERE group_id = '" . (int) $customer_group_id . "'");
        $this->db->query("DELETE FROM #__denied_groups WHERE group_id = '" . (int) $customer_group_id . "'");
    }

    public function getCustomerGroup($customer_group_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__customer_group WHERE customer_group_id = '" . (int) $customer_group_id . "' ");

        return $query->row;
    }

    public function getCustomerGroups($data = array()) {
        $sql = "SELECT * FROM #__customer_group  WHERE 1 ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_system'])) {
            $sql .= " AND customer_group_id > '0'";
        }

        $sort_data = array(
            'name',
            'sort_order'
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

        return $query->rows;
    }

    /**
     * 
     * @param type $customer_group_id
     * @return type
     * @deprecated since version 1
     */
    public function getCustomerGroupDescriptions($customer_group_id) {
        $customer_group_data = array();

        $query = $this->db->query("SELECT * FROM #__customer_group_description WHERE customer_group_id = '" . (int) $customer_group_id . "'");

        foreach ($query->rows as $result) {
            $customer_group_data[$result['language_id']] = array(
                'name' => $result['name'],
                'description' => $result['description']
            );
        }

        return $customer_group_data;
    }

    public function getTotalCustomerGroups() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_group");

        return $query->row['total'];
    }

}
