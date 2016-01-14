<?php

class ModelMarketingNewsletterCampaign extends \Core\Model {

    public function addCampaign($data) {
        $this->db->query("insert into #__newsletter_campaign set campaign_name='" . $this->db->escape($data['campaign_name']) . "', "
                . "create_date = now()");
        $campaign_id = $this->db->insertId();

        $this->setCampaignNewsletters($campaign_id, $data['campaign_newsletter']);

        return $campaign_id;
    }

    public function editCampaign($campaign_id, $data) {
        $this->db->query("update #__newsletter_campaign set campaign_name='" . $this->db->escape($data['campaign_name']) . "'"
                . " where campaign_id = '" . (int) $campaign_id . "'");
        $this->setCampaignNewsletters($campaign_id, $data['campaign_newsletter']);
    }

    public function setCampaignNewsletters($campaign_id, $newsletters = array()) {

        $this->db->query("delete from #__newsletter_campaign_newsletter where campaign_id = '" . (int) $campaign_id . "'");
        foreach ($newsletters as $newsletter) {
            try {
            $this->db->query("insert into #__newsletter_campaign_newsletter set campaign_id = '" . (int) $campaign_id . "',"
                    . " newsletter_id = '" . (int) $newsletter['newsletter_id'] . "', "
                    . " send_time = '" . ( $newsletter['send_time'] * 86400) . "'");
            }catch(\Core\Exception $e){
                //continue ... we cannot send same newsletter twice at the same time :-)
            }
        }
    }

    public function deleteCampaign($campaign_id) {
        $this->db->query("delete from #__newsletter_campaign where campaign_id = '" . (int) $campaign_id . "'");
        $this->db->query("delete from #__newsletter_campaign_subscriber where campaign_id = '" . (int) $campaign_id . "'");
        $this->db->query("delete from #__newsletter_campaign_newsletter where campaign_id = '" . (int) $campaign_id . "'");
    }

    public function getTotalCampaigns() {
        $query = $this->db->query("select count(*) as total from #__newsletter_campaign")->row;
        return $query['total'];
    }

    public function getCampaign($campaign_id) {
        $campaign = $this->db->query("select * from #__newsletter_campaign where campaign_id = '" . (int) $campaign_id . "'")->row;
        $campaign['campaign_newsletter'] = $this->getCampaignNewsletters($campaign_id);

        return $campaign;
    }

    public function getCampaignNewsletters($campaign_id) {
        $rows = $this->db->query("select nc.newsletter_id, nc.send_time, n.name from #__newsletter_campaign_newsletter nc inner join #__newsletter n using (newsletter_id) where nc.campaign_id='" . (int) $campaign_id . "' order by nc.send_time asc")->rows;
        foreach($rows as &$row){
            $row['send_time'] = $row['send_time'] / 86400;
        }
        return $rows;
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

    public function getSubscriberCount($campaign_id) {
        $sql = $this->db->query("Select count(*) as total from #__subscriber s inner join #__newsletter_campaign_subscriber sg on s.subscriber_id = sg.subscriber_id where sg.campaign_id = '" . (int) $campaign_id . "'")->row;
        return $sql['total'];
    }
    
    public function getNewsletterCount($campaign_id){
         return $this->db->query("select count(*) as total from #__newsletter_campaign_newsletter where campaign_id='" . (int) $campaign_id . "'")->row['total'];
    }

}
