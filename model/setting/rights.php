<?php

class ModelSettingRights extends \Core\Model {
    /*
      SYSTEM Groups

      -1 everybody
      -2 guests (visitors)
      -3 users

     */

    public function getPassword($object_id) {
        $row = $this->db->query("select password from #__allowed_password where object_id= '" . (int) $object_id . "'")->row;
        return (!empty($row['password'])) ? $row['password'] : '';
    }

    public function getRight($object_id, $object_type, $user_id = false) {

        if (!$user_id) {

            $group_id = $this->customer->getGroupId();
            $user_id = $this->customer->getId();
        }else{
            $q = $this->db->query("select * from #__customer where customer_id='" . $user_id . "'");
            $group_id = json_decode($q->row['customer_group_id'],1);
        }

        $viewable = false;

        if (!empty($user_id)) {

            //then usergroup, users, everybody

            $query = $this->db->query("SELECT object_id FROM #__allowed_groups WHERE object_id = '" . (int) $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND group_id IN (-1,-3," . implode(",", $group_id) . ")");

            if ($query->row) {
                $viewable = true;
            }

            $query = $this->db->query("SELECT object_id FROM #__allowed_users WHERE object_id = '" . $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND user_id = '" . $user_id . "'");

            if ($query->row) {
                $viewable = true;
            }

            $query = $this->db->query("SELECT object_id FROM #__denied_groups WHERE object_id = '" . (int) $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND group_id IN (-1,-3," . implode(",", $group_id) . ")");

            if ($query->row) {
                $viewable = false;
            }

            $query = $this->db->query("SELECT object_id FROM #__denied_users WHERE object_id = '" . $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND user_id = '" . $user_id . "'");

            if ($query->row) {
                $viewable = false;
            }
        } else {

            //everybody, guests

            $query = $this->db->query("SELECT object_id FROM #__allowed_groups WHERE object_id = '" . (int) $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND group_id IN (-1,-2)");

            if ($query->row) {
                $viewable = true;
            }

            $query = $this->db->query("SELECT object_id FROM #__denied_groups WHERE object_id = '" . (int) $object_id . "' AND object_type = '" . $this->db->escape($object_type) . "' AND group_id IN (-1,-2)");

            if ($query->row) {
                $viewable = false;
            }
        }

        return $viewable;
    }

}
