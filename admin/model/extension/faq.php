<?php

class ModelExtensionFaq extends \Core\Model {

    public function addfaq($data) {
        $this->db->query("INSERT INTO #__faq set date_added = NOW(), status = '" . (int) $data['status'] . "',"
                . " question = '" . $this->db->escape($data['question']) . "', 
            answer = '" . $this->db->escape($data['answer']) . "'");
    }

    public function editfaq($faq_id, $data) {
        $this->db->query("UPDATE #__faq SET question = '" . $this->db->escape($data['question']) . "', answer = '" . $this->db->escape($data['answer']) . "', status = '" . (int) $data['status'] . "' WHERE faq_id = '" . (int) $faq_id . "'");
    }

    public function getfaq($faq_id) {
        $query = $this->db->query("SELECT * FROM #__faq WHERE faq_id = '" . (int) $faq_id . "'");

        if ($query->num_rows) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getAllfaq($data) {
        $sql = "SELECT * FROM #__faq  ";


        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = " question LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }


        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }



        if (isset($data['start']) && isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " ORDER BY date_added DESC LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function deletefaq($faq_id) {
        $this->db->query("DELETE FROM #__faq WHERE faq_id = '" . (int) $faq_id . "'");
        $this->db->query("DELETE FROM #__url_alias WHERE query = 'faq_id=" . (int) $faq_id . "'");
    }

    public function getTotalfaq($data) {
        $sql = "SELECT COUNT(*) AS total FROM #__faq   ";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "question LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }


        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

}
