<?php
class ModelExtensionTestimonial extends \Core\Model {
	public function getTestimonial($testimonial_id) {
		$query = $this->db->query("SELECT * FROM #__testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		return $query->row;
	}

	public function getTestimonials($start = 0, $limit = 20) {
		$query = $this->db->query("SELECT * FROM  #__testimonial where status = 1  ORDER BY featured DESC, sort_order ASC, date_added DESC LIMIT " . (int)$start . ", " . (int)$limit);

		return $query->rows;
	}

	public function getTotalTestimonials() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM  #__testimonial WHERE status = 1");

		if ($query->row) {
			return $query->row['total'];
		} else {
			return FALSE;
		}
	}

	public function addTestimonial($data) {
		$this->db->query("INSERT INTO  #__testimonial SET firstname = '" . $this->db->escape($data['firstname']) . "', "
                        . "lastname = '" . $this->db->escape($data['lastname']) . "', "
                        . "email = '" . $this->db->escape($data['testify_email']) . "', "
                        . "website = '" . $this->db->escape($data['website']) . "', "
                        . "company = '" . $this->db->escape($data['company']) . "', "
                        . "title = '" . $this->db->escape($data['title']) . "', "
                        . "rating = '" . (int)$data['rating'] . "', "
                        . "testimony = '" . $this->db->escape($data['testimony']) . "', "
                        . "status = '" . (int)$this->config->get('testimonial_auto_approve') . "', "
                        . "date_added = NOW(), "
                        . "date_modified = NOW()");

                $this->cache->delete('testimonial');
	}

	public function emailExists($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM  #__testimonial WHERE LCASE(email) = LCASE('" . $this->db->escape($email) . "')");

		if ($query->row) {
			return $query->row['total'];
		} else {
			return FALSE;
		}
	}
}