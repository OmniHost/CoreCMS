<?php
class ModelDesignLayout extends \Core\Model {
	public function addLayout($data) {
            
		$this->db->query("INSERT INTO #__layout SET name = '" . $this->db->escape($data['name']) . "', template='" . $this->db->escape($data['override'])."'");

		$layout_id = $this->db->getLastId();

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO #__layout_route SET layout_id = '" . (int)$layout_id . "',route = '" . $this->db->escape($layout_route['route']) . "'");
			}	
		}
                
                if (isset($data['layout_module'])) {
			foreach ($data['layout_module'] as $layout_module) {
				$this->db->query("INSERT INTO #__layout_module SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape($layout_module['code']) . "', position = '" . $this->db->escape($layout_module['position']) . "', sort_order = '" . (int)$layout_module['sort_order'] . "'");
			}
		}
                return $layout_id;
	}

	public function editLayout($layout_id, $data) {
		$this->db->query("UPDATE #__layout SET name = '" . $this->db->escape($data['name']) . "', template='" . $this->db->escape($data['override'])."' WHERE layout_id = '" . (int)$layout_id . "'");

		$this->db->query("DELETE FROM #__layout_route WHERE layout_id = '" . (int)$layout_id . "'");

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO #__layout_route SET layout_id = '" . (int)$layout_id . "', route = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}
                
                $this->db->query("DELETE FROM #__layout_module WHERE layout_id = '" . (int)$layout_id . "'");

		if (isset($data['layout_module'])) {
			foreach ($data['layout_module'] as $layout_module) {
				$this->db->query("INSERT INTO #__layout_module SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape($layout_module['code']) . "', position = '" . $this->db->escape($layout_module['position']) . "', sort_order = '" . (int)$layout_module['sort_order'] . "'");
			}
		}
	}

	public function deleteLayout($layout_id) {
		$this->db->query("DELETE FROM #__layout WHERE layout_id = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM #__layout_route WHERE layout_id = '" . (int)$layout_id . "'");	
                
		$this->db->query("DELETE FROM #__layout_module WHERE layout_id = '" . (int)$layout_id . "'");
	}

	public function getLayout($layout_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM #__layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row;
	}

	public function getLayouts($data = array()) {
		$sql = "SELECT * FROM #__layout";

		$sort_data = array('name');	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}					

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getLayoutRoutes($layout_id) {
		$query = $this->db->query("SELECT * FROM #__layout_route WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->rows;
	}

	public function getTotalLayouts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM #__layout");

		return $query->row['total'];
	}	
        
        public function getLayoutModules($layout_id) {
		$query = $this->db->query("SELECT * FROM #__layout_module WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->rows;
	}
}