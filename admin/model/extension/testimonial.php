<?php

class ModelExtensionTestimonial extends \Core\Model {

    public function addTestimonial($data) {
        $this->db->query("INSERT INTO #__testimonial SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', website = '" . $this->db->escape($data['website']) . "', rating = '" . (int) $data['rating'] . "', company = '" . $this->db->escape($data['company']) . "', title = '" . $this->db->escape($data['title']) . "', testimony = '" . $this->db->escape($data['testimony']) . "', featured = '" . (int) $data['featured'] . "', status = '" . (int) $data['status'] . "', sort_order = '" . (int) $data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

        $testimonial_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE #__testimonial SET image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id = '" . (int) $testimonial_id . "'");
        }

        $this->cache->delete('testimonial');
    }

    public function editTestimonial($testimonial_id, $data) {
        $this->db->query("UPDATE #__testimonial SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', website = '" . $this->db->escape($data['website']) . "', company = '" . $this->db->escape($data['company']) . "', title = '" . $this->db->escape($data['title']) . "', rating = '" . (int) $data['rating'] . "', testimony = '" . $this->db->escape($data['testimony']) . "', featured = '" . (int) $data['featured'] . "', status = '" . (int) $data['status'] . "', sort_order = '" . (int) $data['sort_order'] . "', date_modified = NOW() WHERE testimonial_id = '" . (int) $testimonial_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE #__testimonial SET image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id = '" . (int) $testimonial_id . "'");
        }

        $this->cache->delete('testimonial');
    }

    public function deleteTestimonial($testimonial_id) {
        $this->db->query("DELETE FROM #__testimonial WHERE testimonial_id = '" . (int) $testimonial_id . "'");

        $this->cache->delete('testimonial');
    }

    public function getTestimonial($testimonial_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__testimonial WHERE testimonial_id = '" . (int) $testimonial_id . "'");

        return $query->row;
    }

    public function getTestimonials($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM #__testimonial";

            $sort_data = array(
                'firstname',
                'lastname',
                'email',
                'website',
                'status',
                'rating',
                'featured',
                'sort_order',
                'date_added'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY date_added";
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

                $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $testimonial_data = $this->cache->get('testimonial');

            if (!$testimonial_data) {
                $query = $this->db->query("SELECT * FROM #__testimonial ORDER BY date_added");

                $testimonial_data = $query->rows;

                $this->cache->set('testimonial', $testimonial_data);
            }

            return $testimonial_data;
        }
    }

    public function getTotalTestimonials() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__testimonial");

        return $query->row['total'];
    }

    public function getTotalTestimonialsAwaitingApproval() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__testimonial WHERE status = '0'");

        return $query->row['total'];
    }

}
