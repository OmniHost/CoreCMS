<?php

class ControllerMarketingCron extends \Core\Controller {

    public function index(&$output) {


        $sendTimeouts = 60 * 60 * 5;
        if ($this->cache->get('cron.marketing.sending') || $this->cache->get('cron.marketing.lastsend')) {
            return;
        }

        $output .= "Sending Newsletters \n";
        $this->load->model('marketing/newsletter', DIR_APPLICATION . 'admin/');
        $this->cache->set('cron.marketing.lastsend', 'cron.marketing.lastsend', $sendTimeouts);
        $this->cache->set('cron.marketing.sending', 'cron.marketing.sending', 60 * 60 * 24);
        //firstly any campaigns???
        $this->sendGroups();
        $this->sendCampaigns();
        $this->cache->delete('cron.marketing.sending');
    }

    protected function sendGroups() {
        $pending = $this->model_marketing_newsletter->getPendingSends();

        foreach ($pending as $send) {
            $send_id = $send['send_id'];
            $newsletter_id = $send['newsletter_id'];
            if ($send['start_time'] < time()) {

                $send_data = $this->get_send($send_id);


                $newsletter_id = $send_data['newsletter_id'];
                $newsletter_data = $this->model_marketing_newsletter->getNewsletter($newsletter_id);

                $batch_limit = 10; // default 10.

                $result = array();
                $result['status'] = true;
                $sent_to = count($send_data['sent_members']);
                $batch_count = 0;
                $send_count = 0;
                foreach ($send_data['unsent_members'] as $unsent_member) {

                    $result = $this->send_out_newsletter($send_id, $unsent_member['subscriber_id']);

                    if ($result['status']) {
                        $batch_count++;
                        $sent_to++;
                    } else {
                        $sent_to = $result['message'];
                    }

                    if (!$result['status']) {
                        // break on fail to send
                        break;
                    }
                    $send_count++;
                }
                if ($result['status']) {
                    $send_data = $this->get_send($send_id);
                    if (!count($send_data['unsent_members'])) {
                        $this->db->query("UPDATE `#__newsletter_send` SET status = 3, finish_time = '" . time() . "' WHERE `send_id` = '" . $this->db->escape($send_id) . "'");
                    }
                }
            }
        }
    }

    protected function sendCampaigns() {

        $campaigns = $this->db->query("SELECT * FROM `#__newsletter_campaign`")->rows;




        if ($campaigns) {

            foreach ($campaigns as &$campaign) {
                $campaign = $this->get_campaign($campaign);

                $sends = array();
                foreach ($campaign['members_rs'] as $member) {
                    $campaign_newsletters = $campaign['newsletter_rs'];
                    $member_next_newsletter = false;
                    if (!$member['current_newsletter_id']) {
                        //echo 'Nothing sent yet.';
                        $member_next_newsletter = current($campaign_newsletters);
                    } else {
                        $x = 0;
                        $member_newsletter = false;
                        foreach ($campaign_newsletters as $campaign_newsletter) {
                            if ($member_newsletter) {
                                $member_next_newsletter = $campaign_newsletter;
                                break;
                            }
                            if ($campaign_newsletter['newsletter_id'] == $member['current_newsletter_id']) {
                                $member_newsletter = true;
                            }
                            $x++;
                        }
                    }

                    if ($member_next_newsletter) {
                        //echo '<strong>'.$member_next_newsletter['subject'] . '</strong> on ';
                        $send_time = $member['join_time'] + $member_next_newsletter['send_time'];

                        if ($send_time <= time()) {

                            // if we missed this send time (ie: it's in the past) then it's time to send this newsletter to this customer.
                            $send_id = $this->model_marketing_newsletter->createSend($member_next_newsletter['newsletter_id'], array(
                                'group_ids' => array(0),
                                'member_id' => $member['subscriber_id'],
                                'campaign_id' => $campaign['campaign_id'],
                                'dont_send_duplicate' => false,
                                'send_later' => time()
                            ));

                            if ($send_id) {
                                $result = $this->send_out_newsletter($send_id, $member['subscriber_id']);
                                // we do it similar to the code in send.php so that if we go over any limits we can at least re-start this single send 
                                $send_data = $this->get_send($send_id);
                                if (!count($send_data['unsent_members'])) {
//                                    $newsletter->send_complete($db, $send_id);
                                    $this->db->query("UPDATE `#__newsletter_send` SET status = 3, finish_time = '" . time() . "' WHERE `send_id` = '" . $this->db->escape($send_id) . "'");
                                    // and update our member status to the next newsletter.
                                    $sql = "UPDATE #__newsletter_campaign_subscriber SET current_newsletter_id = '" . $member_next_newsletter['newsletter_id'] . "' WHERE campaign_id = '" . (int) $campaign['campaign_id'] . "' AND subscriber_id = '" . $member['subscriber_id'] . "'";
                                    $res = $this->db->query($sql);
                                }
                            }
                        }
                        //echo date("d M Y h:i:sa",$send_time);
                    }
                }
            }
        }
    }

    protected function get_campaign($campaign) {

        $sql = $this->db->query("SELECT * FROM #__newsletter_campaign_subscriber cm LEFT JOIN #__subscriber m USING (subscriber_id) WHERE campaign_id = '" . (int) $campaign['campaign_id'] . "'");
        $campaign['members_rs'] = $sql->rows;
        $sql = $this->db->query("SELECT * FROM #__newsletter_campaign_newsletter cm LEFT JOIN #__newsletter n USING (newsletter_id) WHERE campaign_id = '" . (int) $campaign['campaign_id'] . "' ORDER BY send_time ASC");
        $campaign['newsletter_rs'] = $sql->rows;
        return $campaign;
    }

    protected function send_out_newsletter($send_id, $member_id, $newsletter_id = false, $force = false) {


        // status is false if we have gone over limit.
        $status = true;

        if ($status) {
            $send_id = (int) $send_id;
            $member_id = (int) $member_id;

            $send_data = $this->get_send($send_id);

            if (!$force && $send_data['status'] != '1') {
                return false;
            }
            $newsletter_id = $send_data['newsletter_id'];

            $newsletter_data = $this->db->query("select * from #__newsletter where newsletter_id='" . (int) $newsletter_id . "'")->row;

            $member_data = $this->db->query("select * from #__subscriber where subscriber_id='" . (int) $member_id . "'")->row;


            $newsletter_html = html_entity_decode($send_data['full_html']);





            $replace = array(
                "email_subject" => $newsletter_data['subject'],
                "from_name" => $newsletter_data['from_name'],
                "from_email" => $newsletter_data['from_email'],
                "to_email" => $member_data['email'],
                "sent_date" => date("jS M, Y"),
                "sent_month" => date("M Y"),
                "unsubscribe" => $this->config->get('config_url') . 'ext/unsubcribe?sid=' . $send_id . '&mid=' . $member_data['subscriber_id'] . '&hash=' . md5("Unsub " . $member_data['subscriber_id'] . "from $send_id"),
                "view_online" => $this->config->get('config_url') . 'ext/view?id=' . $send_data['newsletter_id'] . '&sid=' . $send_id . '&mid=' . $member_data['subscriber_id'] . '&hash=' . md5("view link " . $member_data['subscriber_id'] . "from $send_id"),
                /*   "link_account" => $this->settings['url_update'], */
                "member_id" => $member_data['subscriber_id'],
                "send_id" => $send_id,
                "MEMBER_HASH" => md5("Member Hash for $send_id with member_id $member_id"),
                "first_name" => $member_data['firstname'],
                "last_name" => $member_data['lastname'],
                "email" => $member_data['email'],
            );

            foreach ($replace as $key => $val) {
                $newsletter_html = preg_replace('/\{' . strtoupper(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/' . strtoupper(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/\{' . strtolower(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
                $newsletter_html = preg_replace('/' . strtolower(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
            }

            $options = array(
                "bounce_email" => $newsletter_data['bounce_email'],
                "message_id" => "Newsletter-$send_id-$member_id-" . md5("bounce check for $member_id in send $send_id"),
            );


            $send_email_status = $this->send_email($replace['to_email'], $replace['email_subject'], $newsletter_html, $replace['from_email'], $replace['from_name'], $options);

            if ($send_email_status) {
                // all worked correctly.
                $sql = "UPDATE #__newsletter_subscriber SET `status` = 3, sent_time = '" . time() . "' WHERE `subscriber_id` = '" . $this->db->escape($member_data['subscriber_id']) . "' AND send_id = '" . $send_id . "' LIMIT 1";
                $res = $this->db->query($sql);
            } else {
                // something failed. mark is as bounced as well. or do we? hm
                $sql = "UPDATE #__newsletter_subscriber SET `status` = 4, sent_time = '" . time() . "', bounce_time = '" . time() . "' WHERE `member_id` = '" . $this->db->escape($member_data['member_id']) . "' AND send_id = '" . $send_id . "' LIMIT 1";
                $res = $this->db->query($sql);
            }
        }

        return array(
            'send_status' => $send_email_status,
            'status' => $status,
            'message' => (!$status) ? 'Email limit exceeded - please try again later (or if you have setup cron, we will try automatically for you).' : '',
        );
    }

    protected function get_send($send_id) {

        $sql = "SELECT * FROM `#__newsletter_send` WHERE `send_id` = '" . $this->db->escape($send_id) . "'";
        $send = $this->db->query($sql)->row;

        $sql = "SELECT * FROM `#__newsletter_subscriber` nm LEFT JOIN `#__subscriber` m USING (subscriber_id) WHERE nm.send_id = '" . $this->db->escape($send_id) . "' AND nm.status = 1 AND m.unsubscribe_date = '0000-00-00'";
        $send['unsent_members'] = $this->db->query($sql)->rows;
        $sql = "SELECT * FROM `#__newsletter_subscriber` nm LEFT JOIN `#__subscriber` m USING (subscriber_id) WHERE nm.send_id = '" . $this->db->escape($send_id) . "' AND nm.status != 1";
        $send['sent_members'] = $this->db->query($sql)->rows;

        $sql = "SELECT * FROM `#__newsletter_subscriber` nm LEFT JOIN `#__subscriber` m USING (subscriber_id) WHERE nm.send_id = '" . $this->db->escape($send_id) . "' AND m.unsubscribe_date != '0000-00-00' AND m.unsubscribe_send_id = '" . $this->db->escape($send_id) . "'";
        $send['unsub_members'] = $this->db->query($sql)->rows;
        $sql = "SELECT * FROM `#__newsletter_subscriber` nm LEFT JOIN `#__subscriber` m USING (subscriber_id) WHERE nm.send_id = '" . $this->db->escape($send_id) . "' AND nm.open_time > 0";
        $send['opened_members'] = $this->db->query($sql)->rows;
        $sql = "SELECT * FROM `#__newsletter_subscriber` nm LEFT JOIN `#__subscriber` m USING (subscriber_id) WHERE nm.send_id = '" . $this->db->escape($send_id) . "' AND nm.bounce_time > 0";
        $send['bounce_members'] = $this->db->query($sql)->rows;

        return $send;
    }

    protected function send_email($to_email, $email_subject, $newsletter_html, $from_email, $from_name, $options = array()) {

        $mail = new \Core\Mail();
        $mail->tags = array('Newsletter System');
        $mail->mandrill_key = $this->config->get('config_mandrill_key');
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->username = $this->config->get('config_mail_smtp_username');
        $mail->password = $this->config->get('config_mail_smtp_password');
        $mail->port = $this->config->get('config_mail_smtp_port');
        $mail->timeout = $this->config->get('config_mail_smtp_timeout');
        if (!empty($options['message_id'])) {
            $mail->message_id = $options['message_id'];
        }
        $mail->setFrom($this->config->get('config_email'));
        if (!empty($options['bounce_email'])) {
            $mail->setFrom($options['bounce_email']);
        }
        $mail->setTo($to_email);

        $mail->setReplyTo($from_email, $from_name);
        $mail->setSender($from_name);
        $mail->setSubject($email_subject);
        $mail->setHtml($newsletter_html);

        try {
            $mail->send();
            return true;
        } catch (\Core\Exception $e) {

            return false;
        }
    }

}
