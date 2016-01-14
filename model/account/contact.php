<?php

class ModelAccountContact extends \Core\Model {

    public function addContact($data) {
        if(empty($data['custom_field'])){
            $data['custom_field'] = array();
        }
        $ip = $this->request->server['REMOTE_ADDR'];

        $this->db->query("INSERT INTO #__contact SET "
                . " firstname = '" . $this->db->escape($data['name']) . "', "
                . " email = '" . $this->db->escape($data['email']) . "', "
                . " enquiry = '" . $this->db->escape($data['enquiry']) . "', "
                . " custom_field = '" . $this->db->escape(serialize($data['custom_field'])) . "', "
                . " ipaddress = '$ip',"
                . " date_added = now()");
    }

}

?>
