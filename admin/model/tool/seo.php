<?php

class ModelToolSeo extends \Core\Model {

    public function getUrl($query_string) {
        $query = $this->db->fetchRow("select keyword from #__url_alias where query = '" . $this->db->escape($query_string) . "'");
        return $query['keyword'];
    }
    
    public function getSlug($keyword){
         $query = $this->db->fetchRow("select query from #__url_alias where keyword = '" . $this->db->escape($keyword) . "'");
        return $query['query'];
    }

    public function setUrl($query_string, $keyword) {
        $this->deleteUrl($query_string);
        $this->db->query("insert into #__url_alias set query='" . $this->db->escape($query_string) . "', keyword='" . $this->db->escape($keyword) . "'");
    }

    public function hasUrl($slugname) {
        $q = $this->db->query("select count(*) as cnt from #__url_alias where keyword = '" . $this->db->escape($slugname) . "'");
        $cnt = $q->row['cnt'];
        return ($cnt) ? true : false;
    }

    public function deleteUrl($query_string) {
        $this->db->query("delete from #__url_alias where `query` = '" . $this->db->escape($query_string) . "'");
    }

    public function getUniqueSlug($name) {
        $slugname = slug($name);
        $q = $this->db->fetchRow("select count(*) as cnt from #__url_alias where `keyword` like '" . $this->db->escape($slugname) . "'");
        if ($q['cnt']) {
            $q = $this->db->query("select count(*) as cnt from #__url_alias where `keyword` REGEXP '^" . $this->db->escape($slugname) . "[0-9]*'");
            $cnt = $q->row['cnt'];
            if ($cnt > 0) {
                $slugname .= $cnt;
            }
        }
        return $slugname;
    }

}
