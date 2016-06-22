<?php

class ModelCmsBanner extends \Core\Model {

    public function getBanner($banner_id) {
        $query = $this->db->query("SELECT * FROM #__banner_image  WHERE banner_id = '" . (int) $banner_id . "'  ORDER BY sort_order ASC");

        return $query->rows;
    }

    public function getBanners($data = array()) {
        $sql = "SELECT title, image, link, description FROM #__banner_image bi";
       

        $sql .=" LEFT JOIN #__banner b ON (b.banner_id  = bi.banner_id) WHERE 1";

        if (!empty($data['filter_banner_id'])) {
            $sql .=" AND b.banner_id = '" . (int) $data['filter_banner_id'] . "'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND b.name LIKE '" . $this->db->escape($data['filter_name']) . "'";
        }

        $sql .=" ORDER BY bi.sort_order ASC";

        $query = $this->db->query($sql);

        return $query->rows;
    }
    
    public function getBannerDetail($banner_id) {
        
        
        $query = $this->db->query("SELECT DISTINCT * FROM #__banner WHERE banner_id = '" . (int) $banner_id . "'");

        $result = $query->row;
        $query = $this->db->query("SELECT * FROM #__banner_image  WHERE banner_id = '" . (int) $banner_id . "'  ORDER BY sort_order ASC limit 1")->row;
        
        if(!empty($query['image'])){
            $result['image'] = $query['image'];
       }
        
        return $result;
    }

}
