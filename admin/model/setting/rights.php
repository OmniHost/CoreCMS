<?php

class ModelSettingRights extends \Core\Model {

    public function getAllowedGroups($id, $type) {

        $right_data = array();

        $query = $this->db->query("SELECT * FROM #__customer_group cg  LEFT JOIN #__allowed_groups ag ON (cg.customer_group_id = ag.group_id) WHERE  ag.object_id = '" . $id . "' AND ag.object_type = '" . $type . "' ORDER BY cg.name ASC");

        foreach ($query->rows as $result) {
            $right_data[] = $result['customer_group_id'];
        }

        return $right_data;
    }

    public function setAllowedGroups($id, $type, $groups) {
        $this->db->query("delete from #__allowed_groups where object_id = '" . (int) $id . "' and object_type = '" . $this->db->escape($type) . "'");
        foreach ($groups as $group) {
            $this->db->query("insert into #__allowed_groups set object_id = '" . (int) $id . "', object_type = '" . $this->db->escape($type) . "', group_id = '" . (int) $group . "'");
        }
    }

    public function getDeniedGroups($id, $type) {

        $right_data = array();

        $query = $this->db->query("SELECT * FROM #__customer_group cg LEFT JOIN #__denied_groups dg ON (cg.customer_group_id = dg.group_id) WHERE  dg.object_id = '" . $id . "' AND dg.object_type = '" . $type . "' ORDER BY cg.name ASC");

        foreach ($query->rows as $result) {
            $right_data[] = $result['customer_group_id'];
        }

        return $right_data;
    }
    
    public function setDeniedGroups($id, $type, $groups) {
        $this->db->query("delete from #__denied_groups where object_id = '" . (int) $id . "' and object_type = '" . $this->db->escape($type) . "'");
        foreach ($groups as $group) {
            $this->db->query("insert into #__denied_groups set object_id = '" . (int) $id . "', object_type = '" . $this->db->escape($type) . "', group_id = '" . (int) $group . "'");
        }
    }

    /**
     * 
     * @param type $id
     * @param type $type
     * @return type
     * @TODO
     */
    public function getAllowedUsers($id, $type) {

        $right_data = array();

        $query = $this->db->query("SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM #__customer c LEFT JOIN #__allowed_users au ON (c.customer_id = au.user_id) WHERE au.object_id = '" . $id . "' AND au.object_type = '" . $type . "' ORDER BY name ASC");

        foreach ($query->rows as $result) {
            $right_data[] = $result['customer_id'];
        }

        return $right_data;
    }

    public function setAllowedUsers($id, $type, $allowed_users = array()) {
        $this->db->query("delete from #__allowed_users where object_id = '" . (int) $id . "' and object_type = '" . $this->db->escape($type) . "'");

        foreach ($allowed_users as $user_id) {
            $this->db->query("INSERT INTO #__allowed_users SET object_id = '" . (int) $id . "', object_type = '" . $this->db->escape($type) . "', user_id = '" . (int) $user_id . "'");
        }
    }

    /**
     * 
     * @param type $id
     * @param type $type
     * @return type
     * @TODO
     */
    public function getDeniedUsers($id, $type) {

        $right_data = array();

        $query = $this->db->query("SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM #__customer c LEFT JOIN #__denied_users du ON (c.customer_id = du.user_id) WHERE du.object_id = '" . $id . "' AND du.object_type = '" . $type . "' ORDER BY name ASC");

        foreach ($query->rows as $result) {
            $right_data[] = $result['customer_id'];
        }

        return $right_data;
    }

    public function setDeniedUsers($id, $type, $users = array()) {
        $this->db->query("delete from #__denied_users where object_id = '" . (int) $id . "' and object_type = '" . $this->db->escape($type) . "'");

        foreach ($users as $user_id) {
            $this->db->query("INSERT INTO #__denied_users SET object_id = '" . (int) $id . "', object_type = '" . $this->db->escape($type) . "', user_id = '" . (int) $user_id . "'");
        }
    }

    public function getAccessPassword($id){
        $row = $this->db->query("select password from #__allowed_password where object_id= '" . (int) $id . "'")->row;
        return (!empty($row['password']))?$row['password']:'';
    }
    
    public function setAccessPassword($id,$password){
        $row = $this->db->query("delete from #__allowed_password where object_id= '" . (int) $id . "'")->row;
        if($password){
            $this->db->query("insert into #__allowed_password set object_id='" . (int)$id . "', password='" . $this->db->escape($password) . "'");
        }
    }
        
        
}
