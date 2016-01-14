<?php

class ModelMarketingNewsletterGroup extends \Core\Model {

    public function addGroup($data) {
        $this->db->query("insert into #__newsletter_group set group_name='" . $this->db->escape($data['group_name']) . "', public = '" . (int) $data['public'] . "'");
        return $this->db->insertId();
    }

    public function editGroup($group_id, $data) {
        $this->db->query("update #__newsletter_group set group_name='" . $this->db->escape($data['group_name']) . "', public = '" . (int) $data['public'] . "' where group_id = '" . (int) $group_id . "'");
    }

    public function deleteGroup($group_id) {
        $this->db->query("delete from #__newsletter_group where group_id = '" . (int) $group_id . "'");
        $this->db->query("delete from #__subscriber_group where group_id = '" . (int) $group_id . "'");
    }

    public function getTotalGroups() {
        $query = $this->db->query("select count(*) as total from #__newsletter_group")->row;
        return $query['total'];
    }
    
    public function getGroup($group_id){
        return $this->db->query("select * from #__newsletter_group where group_id = '" . (int)$group_id . "'")->row;
    }

    public function getGroups($data = array()) {
        $sql = "select * from #__newsletter_group";
        
        $sql .= " order by group_name asc ";

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

    
    public function getSubscriberCount($group_id){
        $sql = $this->db->query("Select count(*) as total from #__subscriber s inner join #__subscriber_group sg on s.subscriber_id = sg.subscriber_id where sg.group_id = '" . (int)$group_id . "'")->row;
        return $sql['total'];
    }
}
