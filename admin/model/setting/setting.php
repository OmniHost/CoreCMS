<?php
class ModelSettingSetting extends \Core\Model {
	public function getSetting($code) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM #__setting WHERE  `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = unserialize($result['value']);
			}
		}

		return $setting_data;
	}

	public function editSetting($code, $data) {
		$this->db->query("DELETE FROM `#__setting` WHERE  `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO #__setting SET  `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO #__setting SET  `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
				}
			}
		}
	}

	public function deleteSetting($code) {
		$this->db->query("DELETE FROM #__setting WHERE  `code` = '" . $this->db->escape($code) . "'");
	}

	public function editSettingValue($code = '', $key = '', $value = '') {
		if (!is_array($value)) {
			$this->db->query("UPDATE #__setting SET `value` = '" . $this->db->escape($value) . "' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' ");
		} else {
			$this->db->query("UPDATE #__setting SET `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "'");
		}
	}
}
