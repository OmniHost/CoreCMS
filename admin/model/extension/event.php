<?php
class ModelExtensionEvent extends \Core\Model {
	public function addEvent($code, $trigger, $action) {
		$this->db->query("INSERT INTO #__event SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
	}

	public function deleteEvent($code) {
		$this->db->query("DELETE FROM #__event WHERE `code` = '" . $this->db->escape($code) . "'");
	}
}