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

    public function JsHash($String, $Secure = TRUE) {
        if ($Secure === TRUE)
            $Secure = 'md5';

        switch ($Secure) {
            case 'sha1':
                return sha1($String);
                break;
            case 'md5':
            case FALSE:

                return md5($String);
            default:
                return hash($Secure, $String);
        }
    }
}
