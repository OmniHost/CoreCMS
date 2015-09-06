<?php

class ModelToolUpload extends \Core\Model {

    public function addUpload($name, $filename) {
        $code = sha1(uniqid(mt_rand(), true));

        $this->db->query("INSERT INTO `#__upload` SET `name` = '" . $this->db->escape($name) . "', `filename` = '" . $this->db->escape($filename) . "', `code` = '" . $this->db->escape($code) . "', `date_added` = NOW()");

        return $code;
    }

    public function getUploadByCode($code) {
        $query = $this->db->query("SELECT * FROM `#__upload` WHERE code = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

}
