<?php

class ModelModuleFaq extends \Core\Model {

    public function getAllFaq($data) {
        $sql = "SELECT * FROM #__faq  WHERE status = '1' ORDER BY sort_order ASC, date_added ASC";

        if (isset($data['start']) && isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalFaq() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__faq where status=1");

        return $query->row['total'];
    }

}
