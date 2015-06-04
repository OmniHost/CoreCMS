<?php

class ModelReportMap extends \Core\Model {

    // Map
    public function getTotalUsersByCountry($code) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer cr LEFT JOIN #__country cy ON (cr.country_id = cy.country_id) WHERE cy.iso_code_2 LIKE '" . $this->db->escape($code) . "'");

        return $query->row['total'];
    }

    public function getTotalOnlineUsersByCountry($code) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__customer_online co LEFT JOIN #__customer cr ON (co.customer_id = cr.customer_id) LEFT JOIN #__country cy ON (cr.country_id = cy.country_id) WHERE cy.iso_code_2 LIKE '" . $this->db->escape($code) . "'");

        return $query->row['total'];
    }

    public function mapTotalUsers(){
        $query = $this->db->query("select c.iso_code_2, count(cu.customer_id) as total from #__country c left join #__customer cu on c.country_id = cu.country_id group by c.iso_code_2");
        return $query->rows;
    }
    
    public function mapTotalOnlineUsers(){
        $query = $this->db->query("select c.iso_code_2, count(co.customer_id) as total from #__country c left join #__customer cu on c.country_id = cu.country_id left join #__customer_online co on cu.customer_id = co.customer_id group by c.iso_code_2");
        return $query->rows;
    }
}
