<?php

class ModelMarketingSubscriber extends \Core\Model {
    
    public function addSubscriber($data){
        $this->db->query("insert into #__subscriber set email = '" . $this->db->escape($data['email']) . "', "
                . "opt_in = '" . (int)$data['opt_in'] . "', date_created = now(), ip_address = '0.0.0.0'");
    }
    
    public function editSubscriber($subscriber_id, $data){
         $this->db->query("update #__subscriber set email = '" . $this->db->escape($data['email']) . "', "
                . "opt_in = '" . (int)$data['opt_in'] . "', date_modified = now() where subscriber_id = '" . (int)$subscriber_id . "'");
    }
    
    public function deleteSubscriber($subscriber_id){
        $this->db->query("delete from #__subscriber where subscriber_id = '" . (int)$subscriber_id . "'");
    }
    
    public function getTotalSubscribers($filter_data){
        $sql = "Select count(*) as total from #__subscriber where 1";
        if(isset($data['email'])){
            $sql .= " and email like '" . $this->db->escape($filter_data['email']) . "%' ";
        }
        if(isset($data['filter_date_added'])){
            $sql .= " and DATE(date_created) = DATE('" . $this->db->escape($filter_data['filter_date_added']) . "') ";
        }
         if(isset($data['filter_opt_in'])){
            $sql .= " and opt_in = '" . (int)$data['filter_opt_in'] . "' ";
        }
        
       $query = $this->db->query($sql);
       return $query->row['total'];
    }
    
    public function getSubscribers($filter_data){
        $sql = "Select * from #__subscriber where 1";
        if(isset($data['email'])){
            $sql .= " and email like '" . $this->db->escape($filter_data['email']) . "%' ";
        }
        if(isset($data['filter_date_added'])){
            $sql .= " and DATE(date_created) = DATE('" . $this->db->escape($filter_data['filter_date_added']) . "') ";
        }
        if(isset($data['filter_opt_in'])){
            $sql .= " and opt_in = '" . (int)$data['filter_opt_in'] . "' ";
        }
        
        $sort_data = array(
			'email',
			'ip_address',
			'date_created'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY email";
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

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
    }
    
    public function getSubscriber($subscriber_id){
        $query = $this->db->query("select * from #__subscriber where subscriber_id = '" . (int) $subscriber_id . "'");
        return $query->row;
    }

    public function hasEntry($email, $id = false){
        $where = '';
        if($id){
            $where = " and subscriber_id != '" . (int)$id . "'";
        }
        $query = $this->db->query("select count(*) as total from #__subscriber where email = '" . $this->db->escape($email) . "' " . $where);
        return $query->row['total'];
    }
    
}
