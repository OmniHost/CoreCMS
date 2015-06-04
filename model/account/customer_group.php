<?php
class ModelAccountCustomerGroup extends \Core\Model {
	public function getCustomerGroup($customer_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM #__customer_group  WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row;
	}

	public function getCustomerGroups() {
		$query = $this->db->query("SELECT * FROM #__customer_group  ORDER BY sort_order ASC, name ASC");

		return $query->rows;
	}
}