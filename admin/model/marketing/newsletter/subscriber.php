<?php

class ModelMarketingNewsletterSubscriber extends \Core\Model {

    public function addSubscriber($data) {
        $this->db->query("insert into #__subscriber set firstname='" . $this->db->escape($data['firstname']) . "', lastname='" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', "
                . "opt_in = '" . (int) $data['opt_in'] . "', date_created = now(), ip_address = '0.0.0.0'");
        $subscriber_id = $this->db->insertId();
        if (!empty($data['group_id'])) {
            foreach ($data['group_id'] as $group_id) {
                $this->db->query("insert into #__subscriber_group set subscriber_id='" . (int) $subscriber_id . "', group_id='" . (int) $group_id . "'");
            }
        }
        if(!empty($data['campaign_id'])){
            foreach($data['campaign_id'] as $campaign_id){
                $this->db->query("insert into #__newsletter_campaign_subscriber set subscriber_id='" . (int) $subscriber_id . "', campaign_id='" . (int) $campaign_id . "', current_newsletter_id='0', join_time = '" . time() . "'");
            }
        }
    }

    public function editSubscriber($subscriber_id, $data) {
        $this->db->query("update #__subscriber set firstname='" . $this->db->escape($data['firstname']) . "', lastname='" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', "
                . "opt_in = '" . (int) $data['opt_in'] . "', date_modified = now() where subscriber_id = '" . (int) $subscriber_id . "'");
        $this->db->query("delete from #__subscriber_group where subscriber_id = '" . (int) $subscriber_id . "'");
        if (!empty($data['group_id'])) {
            foreach ($data['group_id'] as $group_id) {
                $this->db->query("insert into #__subscriber_group set subscriber_id='" . (int) $subscriber_id . "', group_id='" . (int) $group_id . "'");
            }
        }
        
        //Campaigns::
        //What campaigns are currently active?
       
        if(empty($data['campaign_id'])){
            $this->db->query("delete from #__newsletter_campaign_subscriber where subscriber_id = '" . (int)$subscriber_id . "'");
        }else{
             $ex_query = $this->db->query("select * from #__newsletter_campaign_subscriber where subscriber_id = '" . (int)$subscriber_id . "'")->rows;
             $existing = array();
             foreach($ex_query as $ex){
                 $existing[$ex['campaign_id']] = $ex['campaign_id'];
             }
             foreach($data['campaign_id'] as $campaign_id){
                 if(isset($existing['campaign_id'])){
                     unset($existing['campaign_id']);
                 }else{
                      $this->db->query("insert into #__newsletter_campaign_subscriber set subscriber_id='" . (int) $subscriber_id . "', campaign_id='" . (int) $campaign_id . "', current_newsletter_id='0', join_time = '" . time() . "'");
                 }
             }
             if(count($existing)){
                 $this->db->query("delete from #__newsletter_campaign_subscriber where campaign_id in (" . implode(",", $existing) . ") and subscriber_id = '" . (int)$subscriber_id . "'");
             }
        }
    }

    public function deleteSubscriber($subscriber_id) {
        $this->db->query("delete from #__subscriber where subscriber_id = '" . (int) $subscriber_id . "'");
        $this->db->query("delete from #__subscriber_group where subscriber_id = '" . (int) $subscriber_id . "'");
        $this->db->query("delete from #__newsletter_campaign_subscriber where subscriber_id = '" . (int)$subscriber_id . "'");
    }

    public function getTotalSubscribers($filter_data) {
        $sql = "Select count(*) as total from #__subscriber where 1";
        if (!empty($data['email'])) {
            $sql .= " and email like '" . $this->db->escape($filter_data['email']) . "%' ";
        }
        if (!empty($data['firstname'])) {
            $sql .= " and firstname like '" . $this->db->escape($filter_data['firstname']) . "%' ";
        }
        if (!empty($data['lastname'])) {
            $sql .= " and lastname like '" . $this->db->escape($filter_data['lastname']) . "%' ";
        }
        if (!empty($data['filter_date_added'])) {
            $sql .= " and DATE(date_created) = DATE('" . $this->db->escape($filter_data['filter_date_added']) . "') ";
        }
        if (isset($data['filter_opt_in']) && $data['filter_opt_in']) {

            $sql .= " and unsubsribe_send_id = '0' ";
        }
        if (isset($data['filter_opt_in']) && !$data['filter_opt_in']) {

            $sql .= " and unsubsribe_send_id > '0' ";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getSubscribers($filter_data) {
        $sql = "Select * from #__subscriber where 1";
        if (!empty($data['email'])) {
            $sql .= " and email like '" . $this->db->escape($filter_data['email']) . "%' ";
        }
        if (!empty($data['firstname'])) {
            $sql .= " and firstname like '" . $this->db->escape($filter_data['firstname']) . "%' ";
        }
        if (!empty($data['lastname'])) {
            $sql .= " and lastname like '" . $this->db->escape($filter_data['lastname']) . "%' ";
        }
        if (!empty($data['filter_date_added'])) {
            $sql .= " and DATE(date_created) = DATE('" . $this->db->escape($filter_data['filter_date_added']) . "') ";
        }
        if (isset($data['filter_opt_in']) && $data['filter_opt_in']) {

            $sql .= " and unsubsribe_send_id = '0' ";
        }
        if (isset($data['filter_opt_in']) && !$data['filter_opt_in']) {

            $sql .= " and unsubsribe_send_id > '0' ";
        }

        $sort_data = array(
            'email',
            'ip_address',
            'date_created',
            'firstname',
            'lastname'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY date_created";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
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

    public function getSubscriber($subscriber_id) {
        $query = $this->db->query("select * from #__subscriber where subscriber_id = '" . (int) $subscriber_id . "'");
        $member = $query->row;
        $member['group_id'] = array();
        $member['campaign_id'] = array();
        $groups = $this->db->query("select group_id from #__subscriber_group where subscriber_id = '" . (int) $subscriber_id . "'")->rows;
        foreach($groups as $group){
            $member['group_id'][] = $group['group_id'];
        }
        $campaigns = $this->db->query("select campaign_id from #__newsletter_campaign_subscriber where subscriber_id = '" . (int) $subscriber_id . "'")->rows;
        foreach($campaigns as $campaign){
            $member['campaign_id'][] = $campaign['campaign_id'];
        }
        return $member;
    }

    public function hasEntry($email, $id = false) {
        $where = '';
        if ($id) {
            $where = " and subscriber_id != '" . (int) $id . "'";
        }
        $query = $this->db->query("select count(*) as total from #__subscriber where email = '" . $this->db->escape($email) . "' " . $where);
        return $query->row['total'];
    }

}
