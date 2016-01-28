<?php

class ModelApiApi extends \Core\Model {

    public function login($username, $password) {
        $query = $this->db->query("SELECT * FROM `#__api` WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape($password) . "' AND status = '1'");

        return $query->row;
    }
    
    public function getCredentials($id){
           $query = $this->db->query("SELECT * FROM `#__api` WHERE  api_id='" . (int)$id . "' AND status = '1'");

        return $query->row;   
    }

}
