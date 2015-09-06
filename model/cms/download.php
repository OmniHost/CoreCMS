<?php

class ModelCmsDownload extends \Core\Model {

    public function getDownload($download_id) {
        $query = $this->db->query("SELECT * FROM #__download WHERE download_id = '" . (int) $download_id . "'");
        $download = $query->row;
        if ($download) {
            if (file_exists(DIR_DOWNLOAD . $download['filename'])) {
                $size = filesize(DIR_DOWNLOAD . $download['filename']);

                $i = 0;

                $suffix = array(
                    'B',
                    'KB',
                    'MB',
                    'GB',
                    'TB',
                    'PB',
                    'EB',
                    'ZB',
                    'YB'
                );

                while (($size / 1024) > 1) {
                    $size = $size / 1024;
                    $i++;
                }
                $download['size'] = round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i];
            } else {
                return false;
            }
        }
        return $download;
    }

    public function updateDownloaded($download_id) {
        $this->db->query("UPDATE #__download SET downloaded = (downloaded + 1) WHERE download_id = '" . (int) $download_id . "'");
    }

}
