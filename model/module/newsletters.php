<?php

class ModelModuleNewsletters extends \Core\Model {

    public function subscribe($data) {
        $res = $this->db->query("select count(*) as total from #__subscriber where email='" . $this->db->escape($data['email']) . "'");
        if ($res->row['total'] > 0) {
            $this->db->query("update #__subscriber set opt_in = '1', date_modified = now(), ip_address = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' where email = '" . $this->db->escape($data['email']) . "'");
        } else {
            $this->db->query("INSERT INTO #__subscriber set email = '" . $this->db->escape($data['email']) . "', opt_in = '1', "
                    . "ip_address = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', "
                    . "date_created = now()");
        }
    }
    
    public function unsubscribe($data) {
            $this->db->query("update #__subscriber set opt_in = '0', date_modified = now(), ip_address = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' where email = '" . $this->db->escape($data['email']) . "'");
        
    }

}
