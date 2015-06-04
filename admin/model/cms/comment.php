<?php
class ModelCmsComment extends \Core\Model {
	public function addComment($data) {
	
		$this->db->query("INSERT INTO #__comments SET author = '" . $this->db->escape($data['author']) . "', ams_page_id = '" . (int)$data['ams_page_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$comment_id = $this->db->getLastId();

		$this->cache->delete('ams_page');

		return $comment_id;
	}

	public function editComment($comment_id, $data) {
		
		$this->db->query("UPDATE #__comments SET author = '" . $this->db->escape($data['author']) . "', ams_page_id = '" . (int)$data['ams_page_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE comment_id = '" . (int)$comment_id . "'");

		$this->cache->delete('ams_page');

	}

	public function deleteComment($comment_id) {
		$this->db->query("DELETE FROM #__comments WHERE comment_id = '" . (int)$comment_id . "'");

		$this->cache->delete('ams_page');

	}

	public function getComment($comment_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT id.name FROM #__ams_pages id WHERE id.ams_page_id = r.ams_page_id ) AS page FROM #__comments r WHERE r.comment_id = '" . (int)$comment_id . "'");

		return $query->row;
	}

	public function getComments($data = array()) {
		$sql = "SELECT r.comment_id, id.name, r.author, r.rating, r.status, r.date_added FROM #__comments r LEFT JOIN #__ams_pages id ON (r.ams_page_id = id.ams_page_id) WHERE 1";

		if (!empty($data['filter_page'])) {
			$sql .= " AND id.name LIKE '%" . $this->db->escape($data['filter_page']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== null) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'id.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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

	public function getTotalComments($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM #__comments r LEFT JOIN #__ams_pages id ON (r.ams_page_id = id.ams_page_id) WHERE 1";

		if (!empty($data['filter_page'])) {
			$sql .= " AND id.name LIKE '%" . $this->db->escape($data['filter_page']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== null) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
    
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalCommentsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM #__comments WHERE status = '0'");

		return $query->row['total'];
	}
}