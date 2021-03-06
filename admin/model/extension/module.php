<?php

class ModelExtensionModule extends \Core\Model {

    public function addModule($code, $data) {
        $this->db->query("INSERT INTO `#__module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(serialize($data)) . "'");
    }

    public function editModule($module_id, $data) {
        $this->db->query("UPDATE `#__module` SET `name` = '" . $this->db->escape($data['name']) . "', `setting` = '" . $this->db->escape(serialize($data)) . "' WHERE `module_id` = '" . (int) $module_id . "'");
    }

    public function copyModule($module_id) {
        $setting = $this->getModule($module_id);

        $setting['name'] .= '-copy';

        $query = $this->db->query("SELECT code FROM `#__module` WHERE `module_id` = '" . (int) $module_id . "'");
        $this->addModule($query->row['code'], $setting);
    }

    public function deleteModule($module_id) {
        $this->db->query("DELETE FROM `#__module` WHERE `module_id` = '" . (int) $module_id . "'");
        $this->db->query("DELETE FROM `#__layout_module` WHERE `code` LIKE '%." . (int) $module_id . "'");
    }

    public function getModule($module_id) {
        $query = $this->db->query("SELECT * FROM `#__module` WHERE `module_id` = '" . $this->db->escape($module_id) . "'");

        if ($query->row) {
            return unserialize($query->row['setting']);
        } else {
            return array();
        }
    }

    public function getModules() {
        $query = $this->db->query("SELECT * FROM `#__module` ORDER BY `code`");

        return $query->rows;
    }

    public function getModulesByCode($code) {
        $query = $this->db->query("SELECT * FROM `#__module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");

        return $query->rows;
    }

    public function deleteModulesByCode($code) {
        $this->db->query("DELETE FROM `#__module` WHERE `code` = '" . $this->db->escape($code) . "'");
        $this->db->query("DELETE FROM `#__layout_module` WHERE `code` LIKE '" . $this->db->escape($code) . "' OR `code` LIKE '" . $this->db->escape($code . '.%') . "'");
    }

}
