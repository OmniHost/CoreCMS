<?php
namespace Core;
class User {

    private $user_id;
    private $username;
    private $fullname;
    private $rolename;
    private $user_group_id;
    private $permission = array();

    public function __construct() {
        if (isset($this->session->data['user_id'])) {
            $user_query = $this->db->query("SELECT * FROM #__user WHERE user_id = '" . (int) $this->session->data['user_id'] . "' AND status = '1'");

            if ($user_query->num_rows) {
                $this->user_id = $user_query->row['user_id'];
                $this->username = $user_query->row['username'];
                $this->fullname = $user_query->row['firstname'] . ' ' . $user_query->row['lastname'];
                $this->user_group_id = $user_query->row['user_group_id'];

                $this->db->query("UPDATE #__user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int) $this->session->data['user_id'] . "'");

                $user_group_query = $this->db->query("SELECT * FROM #__user_group WHERE user_group_id = '" . (int) $user_query->row['user_group_id'] . "'");

              
                $this->rolename = $user_group_query->row['name'];
                $permissions = unserialize($user_group_query->row['permission']);

                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        $this->permission[$key] = $value;
                    }
                }
            } else {
                $this->logout();
            }
        }
    }

    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    public function login($username, $password) {
        $user_query = $this->db->query("SELECT * FROM #__user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

        if ($user_query->num_rows) {
            $this->session->data['user_id'] = $user_query->row['user_id'];

            $this->user_id = $user_query->row['user_id'];
            $this->username = $user_query->row['username'];
            $this->user_group_id = $user_query->row['user_group_id'];

            $user_group_query = $this->db->query("SELECT permission FROM #__user_group WHERE user_group_id = '" . (int) $user_query->row['user_group_id'] . "'");

            $permissions = unserialize($user_group_query->row['permission']);

            if (is_array($permissions)) {
                foreach ($permissions as $key => $value) {
                    $this->permission[$key] = $value;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        unset($this->session->data['user_id']);

        $this->user_id = '';
        $this->username = '';

        session_destroy();
    }

    public function hasPermission($key, $value) {
        if (isset($this->permission[$key])) {
            return in_array($value, $this->permission[$key]);
        } else {
            return false;
        }
    }

    public function isLogged() {
        return $this->user_id;
    }

    public function getId() {
        return $this->user_id;
    }

    public function getUserName() {
        return $this->username;
    }

    public function getFullName() {
        return $this->fullname;
    }

    public function getRoleName() {
        return $this->rolename;
    }

    public function getGroupId(){
        return $this->user_group_id;
    }
}
