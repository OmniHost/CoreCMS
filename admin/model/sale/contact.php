<?php

Class ModelSaleContact extends \Core\Model {

    public function getContactinfo($data = array()) {
        $query = $this->db->query("SELECT * FROM #__contact");

        return $query->rows;
    }

    public function getSingledata($id = 0) {
        $query = $this->db->query("SELECT * FROM #__contact where contact_id='" . (int)$id . "' ");

        return $query->rows;
    }

    public function csvdata() {
        $query = $this->db->query("SELECT * FROM #__contact");
        return $query->rows;
    }

    public function insertvalue($view_id) {
        $this->db->query("UPDATE #__contact SET is_read='" . (int)$view_id . "' where contact_id ='" . (int)$view_id . "'");
    }

    public function deletecontact($contact_id) {
        $query = $this->db->query("DELETE  FROM #__contact where contact_id = '" . (int)$contact_id . "' ");

        return $query;
    }

    
}
