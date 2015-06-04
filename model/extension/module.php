<?php
class ModelExtensionModule extends \Core\Model {
	public function getModule($module_id) {
		$query = $this->db->query("SELECT * FROM #__module WHERE module_id = '" . (int)$module_id . "'");
		
		if ($query->row) {
			return unserialize($query->row['setting']);
		} else {
			return array();	
		}
	}		
}