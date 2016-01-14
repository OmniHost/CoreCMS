<?php

class ModelMarketingNewsletter extends \Core\Model {

    public function getNewsletter($newsletter_id) {
        return $this->db->query("select * from #__newsletter where newsletter_id='" . (int) $newsletter_id . "'")->row;
    }

    public function getNewsletters($filter = array()) {
        $sql = "Select * from #__newsletter where 1";
        if (!empty($filter['group_id'])) {
            
        }
        if (!empty($filter['campaign_id'])) {
            
        }

        if (!empty($filter['filter_name'])) {
            $sql .= " and name like '" . $this->db->escape($filter['filter_name']) . "%' ";
        }

        $sort_data = array(
            'name',
            'subject',
            'create_date'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY create_date";
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

    public function getTotalNewsletters($filter) {
        $sql = "Select count(*) as total from #__newsletter where 1";
        if (!empty($filter['group_id'])) {
            
        }
        if (!empty($filter['campaign_id'])) {
            
        }

        if (!empty($filter['filter_name'])) {
            $sql .= " and name like '" . $this->db->escape($filter['filter_name']) . "%' ";
        }
        $query = $this->db->query($sql)->row;
        return $query['total'];
    }

    public function getSendCount($newsletter_id) {
        $query = $this->db->query("select count(*) as total from #__newsletter_subscriber nm inner join #__newsletter_send ns using(send_id) where ns.newsletter_id='" . (int) $newsletter_id . "' and nm.sent_time > 0")->row;
        return $query['total'];
    }

    public function getOpenCount($newsletter_id) {
        $query = $this->db->query("select count(*) as total from #__newsletter_subscriber nm inner join #__newsletter_send ns using(send_id) where ns.newsletter_id='" . (int) $newsletter_id . "' and nm.open_time > 0")->row;
        return $query['total'];
    }

    public function getBounceCount($newsletter_id) {
        $query = $this->db->query("select count(*) as total from #__newsletter_subscriber nm inner join #__newsletter_send ns using(send_id) where ns.newsletter_id='" . (int) $newsletter_id . "' and nm.bounce_time > 0")->row;
        return $query['total'];
    }

    public function getUnsubscribeCount($newsletter_id) {
        $query = $this->db->query("select count(*) as total from #__subscriber nm inner join #__newsletter_send ns on nm.unsubscribe_send_id = ns.send_id where ns.newsletter_id='" . (int) $newsletter_id . "' and nm.unsubscribe_send_id > 0")->row;
        return $query['total'];
    }

    public function addNewsletter($data) {
        $this->db->query("insert into #__newsletter set name = '" . $this->db->escape($data['name']) . "',
            create_date = now(), 
            template = '" . $this->db->escape(json_encode($data['builder_info'])) . "',"
                . "subject = '" . $this->db->escape($data['subject']) . "',"
                . "from_name = '" . $this->db->escape($data['from_name']) . "',"
                . "from_email = '" . $this->db->escape($data['from_email']) . "',"
                . "content = '" . $this->db->escape($data['newsletter']) . "',"
                . "bounce_email = '" . $this->db->escape($data['bounce_email']) . "'");
    }

    public function editNewsletter($newsletter_id, $data) {
        $this->db->query("update #__newsletter set name = '" . $this->db->escape($data['name']) . "',
            template = '" . $this->db->escape(json_encode($data['builder_info'])) . "',"
                . "subject = '" . $this->db->escape($data['subject']) . "',"
                . "from_name = '" . $this->db->escape($data['from_name']) . "',"
                . "from_email = '" . $this->db->escape($data['from_email']) . "',"
                . "content = '" . $this->db->escape($data['newsletter']) . "',"
                . "bounce_email = '" . $this->db->escape($data['bounce_email']) . "'"
                . " where newsletter_id='" . (int) $newsletter_id . "'");
    }

    public function createSend($newsletter_id, $data) {
        $send_members = array();
        foreach ($data['group_ids'] as $group_id) {
            if ($group_id == '0') {
                if (!empty($data['subscriber_id'])) {
                    $sql = "Select * from #__subscriber where subscriber_id = '" . (int) $data['subscriber_id'] . "' and unsubscribe_send_id = '0' ";
                } else {
                    $sql = "Select * from #__subscriber where  unsubscribe_send_id = '0' ";
                }
            } else {
                $sql = "select * from #__subscriber s left join #__subscriber_group sg using(subscriber_id) where sg.group_id = '" . (int) $group_id . "' and  s.unsubscribe_send_id = '0' ";
            }
            $subscribers = $this->db->query($sql)->rows;
            if (!$subscribers) {
                return false;
            }

            foreach ($subscribers as $idx => $subscriber) {
                if ($data['dont_send_duplicate']) {
                    $sent = $this->db->query("SELECT count(*) as total FROM #__newsletter_subscriber nm LEFT JOIN #__newsletter_send s USING (send_id) WHERE nm.subscriber_id = '" . $this->db->escape($subscriber['subscriber_id']) . "' AND s.newsletter_id = '" . (int) $newsletter_id . "'")->row['total'];
                    if (!$sent) {
                        $send_members[$subscriber['subscriber_id']] = $subscriber;
                    }
                } else {
                    $send_members[$subscriber['subscriber_id']] = $subscriber;
                }
            }
        }
        if ($send_members) {
            $campaign_id = (!empty($data['campaign_id']))?$data['campaign_id']:'0';
            $this->db->query("INSERT INTO  #__newsletter_send SET newsletter_id = '" . (int) $newsletter_id . "', campaign_id = '" . (int)$campaign_id . "', `status` = 1, start_time = '" . $data['send_later'] . "'");
            $send_id = $this->db->insertId();




            if ($send_id) {
                foreach ($send_members as $member_id => $subscriber) {
                    $this->db->query("REPLACE INTO `#__newsletter_subscriber` SET send_id = '" . (int) $send_id . "', subscriber_id = '" . (int) $member_id . "', status = 1");
                }

                $newsletter_html = $this->db->query("select * from #__newsletter where newsletter_id = '" . (int) $newsletter_id . "'")->row['content'];
                $newsletter_html = preg_replace('#([\'"])\.\./#', '$1', html_entity_decode($newsletter_html));
                $newsletter_html = $this->fix_image_paths($newsletter_html, $send_id, '');
                $this->db->query("UPDATE `#__newsletter_send` SET full_html = '" . $this->db->escape(htmlentities($newsletter_html)) . "' WHERE send_id = '" . (int) $send_id . "' LIMIT 1");
                unset($newsletter_html);
            }
            return $send_id;
        }

        return false;
    }

    protected function fix_image_paths($data, $send_id = false, $dir = '', $inc_http = true) {
        $dir = trim($dir, '/');
        if (strlen($dir)) {
            $dir.='/';
        }

        if ($inc_http) {
            $base = $this->config->get('config_url') . $dir;
        } else {
            $base = $dir;
        }
        // process links
        // links / iamges keep em different, that way we know if someone has actually CLICKED vs opened.
        foreach (array("href") as $type) {
            preg_match_all('/' . $type . '=(["\'])([^"\']+)\1/', $data, $links);
            if (is_array($links[2])) {
                foreach ($links[2] as $link_id => $l) {
                    //if(!preg_match('/^\{/',$l) && !preg_match('/^#/',$l) && !preg_match('/^mailto:/',$l)){
                    if (!preg_match('/^\{/', $l) && !preg_match('/^#/', $l) && !(preg_match('/^\w+:/', $l) && !preg_match('/^http/', $l))) {
                        //echo $links[0][$link_id] ."<br>";
                        $search = preg_quote($links[0][$link_id], "/");
                        //echo $search."<br>\n";
                        $l = preg_replace("/[\?|&]phpsessid=([\w\d]+)/i", '', $l);
                        $l = ltrim($l, '/');
                        $newlink = ((!preg_match('/^http/', $l)) ? $base : '') . $l;
                        if ($send_id) {
                            // we are sending this out, we need to store a link to this in the db
                            // to record clicks etc..
                            $this->db->query("INSERT INTO `#__newsletter_link` SET link_url = '" . $this->db->escape($newlink) . "'");

                            $link_id = $this->db->insertId();
                            $newlink = $this->config->get('config_catalog') . 'marketing/ext/link?lid=' . $link_id . '&sid={SEND_ID}&mid={MEMBER_ID}&mhash={MEMBER_HASH}';
                        }
                        $replace = $type . '="' . $newlink . '"';
                        //echo $replace."<br>\n";
                        //preg_match('/'.$search."/",$template,$matches);print_r($matches);
                        $data = preg_replace('/' . $search . '/', $replace, $data, 1);
                    }
                }
            }
        }
        foreach (array("src", "background") as $type) {
            preg_match_all('/' . $type . '=(["\'])([^"\']+)\1/', $data, $links);
            //print_r($links);
            if (is_array($links[2])) {
                foreach ($links[2] as $link_id => $l) {
                    //if(!preg_match('/^\{/',$l) && !preg_match('/^#/',$l) && !preg_match('/^mailto:/',$l)){
                    if (!preg_match('/^\{/', $l) && !preg_match('/^#/', $l) && !(preg_match('/^\w+:/', $l) && !preg_match('/^http/', $l))) {
                        //echo $links[0][$link_id] ."<br>";
                        $search = preg_quote($links[0][$link_id], "/");
                        //echo $search."<br>\n";
                        $l = preg_replace("/[\?|&]phpsessid=([\w\d]+)/i", '', $l);
                        $l = ltrim($l, '/');
                        $newlink = ((!preg_match('/^http/', $l)) ? $base : '') . $l;

                        if ($send_id) {
                            // we are sending this out, we need to store a link to this in the db
                            // to record clicks etc..
                            $this->db->query("INSERT INTO `#__newsletter_image` SET image_url = '" . $this->db->escape($newlink) . "'");

                            $link_id = $this->db->insertId(); //($db);
                            $newlink = $this->config->get('config_catalog') . 'marketing/ext/img?id=' . $link_id . '&sid={SEND_ID}&mid={MEMBER_ID}';
                        }

                        $replace = $type . '="' . $newlink . '"';
                        //echo $replace."<br>\n";
                        //preg_match('/'.$search."/",$template,$matches);print_r($matches);
                        $data = preg_replace('/' . $search . '/', $replace, $data, 1);
                    }
                }
            }
        }
        return $data;
    }

    public function getPendingSends($newsletter_id = false, $limit = 10) {
        $newsletter_id = (int) $newsletter_id;
        // find any newsletters that have started a send, but not yet finished.
        // these could be ones scheduled in the future.
        $sql = "SELECT newsletter_id, send_id,start_time,status,finish_time,campaign_id, subject, name FROM `#__newsletter_send` s LEFT JOIN `#__newsletter` n USING (newsletter_id) WHERE finish_time = 0";
        if ($newsletter_id) {
            $sql .= " AND n.newsletter_id = '$newsletter_id'";
        }
        if($limit){
        $sql .= " limit " . $limit;
        }
        $sends = $this->db->query($sql)->rows;

        foreach ($sends as &$send) {
            // work out the progress of this send.
            $send['progress'] = '';
            // work out pending count
            $sql = "SELECT count(subscriber_id) AS item_count FROM `#__newsletter_subscriber` WHERE send_id = '" . $send['send_id'] . "' AND sent_time = 0";
            $unsent = $this->db->query($sql)->row['item_count'];

            // work out total count
            $sql = "SELECT count(subscriber_id) AS item_count FROM `#__newsletter_subscriber` WHERE send_id = '" . $send['send_id'] . "'";
            $total = $this->db->query($sql)->row['item_count'];
            $send['progress'] = "$unsent of $total left to send";
            $send['start_date'] = date("Y-m-d g:i a", $send['start_time']);
        }
        return $sends;
    }

    public function getRecentSends($newsletter_id = false) {
        $newsletter_id = (int) $newsletter_id;
        // find any newsletters that have started a send, but not yet finished.
        // these could be ones scheduled in the future.
        $sql = "SELECT newsletter_id, send_id,start_time,status,finish_time,campaign_id, subject, name FROM `#__newsletter_send` s LEFT JOIN `#__newsletter` n USING (newsletter_id) WHERE finish_time > 0";
        if ($newsletter_id) {
            $sql .= " AND n.newsletter_id = '$newsletter_id'";
        }
        $sql .= " limit 10";
        $sends = $this->db->query($sql)->rows;

        foreach ($sends as &$send) {

            // work out total count
            $sql = "SELECT count(subscriber_id) AS item_count FROM `#__newsletter_subscriber` WHERE send_id = '" . $send['send_id'] . "'";
            $total = $this->db->query($sql)->row['item_count'];
            $send['progress'] = $total;
            $send['start_date'] = date("Y-m-d g:i a", $send['start_time']);
        }
        return $sends;
    }

}
