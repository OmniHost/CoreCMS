<?php

class ModelCmsBanner extends \Core\Model {

    public function addBanner($data) {

        $this->db->query("INSERT INTO #__banner SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', status = '" . (int) $data['status'] . "'");

        $banner_id = $this->db->getLastId();

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $banner_image) {
                $this->db->query("INSERT INTO #__banner_image SET banner_id = '" . (int) $banner_id . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape($banner_image['image']) . "', sort_order = '" . (int) $banner_image['sort_order'] . "', title = '" . $this->db->escape($banner_image['title']) . "', description = '" . $this->db->escape($banner_image['description']) . "'");
            }
        }

        return $banner_id;
    }

    public function editBanner($banner_id, $data) {

        $this->db->query("UPDATE #__banner SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', status = '" . (int) $data['status'] . "' WHERE banner_id = '" . (int) $banner_id . "'");

        $this->db->query("DELETE FROM #__banner_image WHERE banner_id = '" . (int) $banner_id . "'");

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $banner_image) {
                $this->db->query("INSERT INTO #__banner_image SET banner_id = '" . (int) $banner_id . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape($banner_image['image']) . "', sort_order = '" . (int) $banner_image['sort_order'] . "', title = '" . $this->db->escape($banner_image['title']) . "', description = '" . $this->db->escape($banner_image['description']) . "'");
            }
        }
    }

    public function deleteBanner($banner_id) {


        $this->db->query("DELETE FROM #__banner WHERE banner_id = '" . (int) $banner_id . "'");
        $this->db->query("DELETE FROM #__banner_image WHERE banner_id = '" . (int) $banner_id . "'");
    }

    public function getBanner($banner_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__banner WHERE banner_id = '" . (int) $banner_id . "'");

        return $query->row;
    }

    public function getBanners($data = array()) {
        $sql = "SELECT * FROM #__banner";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'status'
        );

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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getBannerImages($banner_id) {
        $banner_image_data = array();

        $banner_image_query = $this->db->query("SELECT * FROM #__banner_image WHERE banner_id = '" . (int) $banner_id . "' ORDER BY sort_order ASC");

        foreach ($banner_image_query->rows as $banner_image) {


            $banner_image_data[] = array(
                'title' => $banner_image['title'],
                'link' => $banner_image['link'],
                'description' => $banner_image['description'],
                'image' => $banner_image['image'],
                'sort_order' => $banner_image['sort_order']
            );
        }

        return $banner_image_data;
    }

    public function getTotalBanners() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__banner");

        return $query->row['total'];
    }

}
