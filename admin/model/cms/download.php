<?php

class ModelCmsDownload extends \Core\Model {

    public function addDownload($data) {

        $this->db->query("INSERT INTO #__download SET name='" . $this->db->escape($data['name']) . "', filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_added = NOW(), date_modified = NOW()");

        $download_id = $this->db->getLastId();

        return $download_id;
    }

    public function editDownload($download_id, $data) {
        $this->db->query("UPDATE #__download SET name='" . $this->db->escape($data['name']) . "', filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_added = NOW() WHERE download_id = '" . (int) $download_id . "'");
    }

    public function deleteDownload($download_id) {
        $this->db->query("DELETE FROM #__download WHERE download_id = '" . (int) $download_id . "'");
    }

    public function getDownload($download_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__download WHERE download_id = '" . (int) $download_id . "' ");

        return $query->row;
    }

    public function getDownloads($data = array()) {
        $sql = "SELECT * FROM #__download WHERE 1 ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'date_added'
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

    public function getTotalDownloads() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__download");

        return $query->row['total'];
    }

}
