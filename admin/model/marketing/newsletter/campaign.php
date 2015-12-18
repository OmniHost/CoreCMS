<?php

class ModelMarketingNewsletterCampaign extends \Core\Model {

    public function addCampaign($data) {
        $this->db->query("insert into #__newsletter_campaign set campaign_name='" . $this->db->escape($data['campaign_name']) . "', "
                . "create_date = now()");
        return $this->db->insertId();
    }

    public function editCampaign($campaign_id, $data) {
        $this->db->query("update #__newsletter_campaign set campaign_name='" . $this->db->escape($data['campaign_name']) . "'"
                . " where campaign_id = '" . (int) $campaign_id . "'");
    }

    public function deleteCampaign($campaign_id) {
        $this->db->query("delete from #__newsletter_campaign where campaign_id = '" . (int) $campaign_id . "'");
        $this->db->query("delete from #__newsletter_campaign_subscriber where campaign_id = '" . (int) $campaign_id . "'");
    }

    public function getTotalCampaigns() {
        $query = $this->db->query("select count(*) as total from #__newsletter_campaign")->row;
        return $query['total'];
    }
    
    public function getCampaign($campaign_id){
        return $this->db->query("select * from #__newsletter_campaign where campaign_id = '" . (int)$campaign_id . "'")->row;
    }

    public function getCampaigns($data = array()) {
        $sql = "select * from #__newsletter_campaign";

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
    
    public function getSubscriberCount($campaign_id){
        $sql = $this->db->query("Select count(*) as total from #__subscriber s inner join #__newsletter_campaign_subscriber sg on s.subscriber_id = sg.subscriber_id where sg.campaign_id = '" . (int)$campaign_id . "'")->row;
        return $sql['total'];
    }

}
