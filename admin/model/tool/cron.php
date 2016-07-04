<?php

Class ModelToolCron extends \Core\Cron {

    public function getTotalCrons() {
        return $this->db->query("select count(*) as total from #__crons")->row['total'];
    }

    public function getCrons($data = array()) {
        $sql = "SELECT * FROM #__crons";




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
    }

}
