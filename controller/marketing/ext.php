<?php

class ControllerMarketingExt extends \Core\Controller {

    public $send_id;
    public $id;
    public $member_id;
    public $mhash_provided = false;
    public $mhash_real = 'NOTHING!';

    public function __construct() {
        parent::__construct();
        $this->send_id = isset($this->request->get['sid']) ? (int) $this->request->get['sid'] : 0;
        $this->id = isset($this->request->get['id']) ? (int) $this->request->get['id'] : 0;
        $this->member_id = isset($this->request->get['mid']) ? (int) $this->request->get['mid'] : 0;

        if (isset($this->request->get['mhash'])) {
            $this->mhash_provided = $this->request->get['mhash'];
            $this->mhash_real = md5("Member Hash for $this->send_id with member_id $this->member_id");
        }

        $this->response->addHeader('Access-Control-Allow-Origin: *');
        $this->response->addHeader('Access-Control-Allow-Methods: *');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    }

    public function link() {
        if ($this->id) {

            $link = $this->db->query("select link_url from #__newsletter_link where link_id='" . (int) $this->id . "'")->row;
            if (!$link) {
                return $this->not_found();
            }
            $this->db->query("update #__newsletter_subscriber set open_time = '" . time() . "' where send_id ='" . (int) $this->send_id . "' and subscriber_id = '" . (int) $this->member_id . "' and open_time = 0");
            $this->db->query("insert into #__newsletter_link_open set link_id='" . (int) $this->id . "', "
                    . "subscriber_id='" . (int) $this->member_id . "', "
                    . "send_id='" . $this->send_id . "', "
                    . "timestamp='" . time() . "'");
            if ($this->mhash_provided && preg_match('#' . preg_quote($this->config->get('config_url'), '#') . '#', $link['link_url'])) {
                // append the send id and member hash to the url:
                $link['link_url'] .= ((strpos($link['link_url'], '?') === false) ? '?' : '&') . 'sid=' . $this->send_id . '&mid=' . $this->member_id . '&mhash=' . $this->mhash_provided;
            }
            // header("Location: " . $link['link_url']);\
            $this->redirect($link['link_url']);
            exit;
        } else {
            $this->not_found();
        }
    }

    public function img() {
        if ($this->id) {
            $link = $this->db->query("select * from #__newsletter_image where image_id='" . (int) $this->id . "'")->row;
            if (!$link) {
                return $this->not_found();
            }
            $this->db->query("update #__newsletter_subscriber set open_time = '" . time() . "' where send_id ='" . (int) $this->send_id . "' and subscriber_id = '" . (int) $this->member_id . "' and open_time = 0");

            $this->redirect("Location: " . $link['image_url']);
            exit;
        }
    }

    public function view() {
        // TODO - replace vars like its a members email.
        // check full version first:
        $provided_hash = isset($this->request->get['hash']) ? $this->request->get['hash'] : false;
        $real_hash = md5("view link " . $this->member_id . "from " . $this->send_id);

        $newsletter_data = $this->db->query("select * from #__newsletter where newsletter_id='" . (int) $this->id . "'")->row;


        $newsletter_html = html_entity_decode($send_data['full_html']);

        $send_data = $this->get_send($this->send_id);

        if (($this->mhash_real == $this->mhash_provided) || ($provided_hash == $real_hash)) {


            $member_data = $this->db->query("select * from #__subscriber where subscriber_id='" . (int) $this->member_id . "'")->row;


            $string = '&sid=' . $this->send_id . '&mid=' . $member_data['subscriber_id'] . '&mhash=' . $mhash_real;



            $replace = array(
                "email_subject" => $newsletter_data['subject'],
                "from_name" => $newsletter_data['from_name'],
                "from_email" => $newsletter_data['from_email'],
                "to_email" => $member_data['email'],
                "sent_date" => date("jS M, Y"), // TODO - read newsletter date
                "sent_month" => date("M Y"), // TODO - read newsletter date
                "member_id" => $member_data['member_id'],
                "send_id" => $send_id,
                "MEMBER_HASH" => md5("Member Hash for $send_id with member_id $member_id"),
                "first_name" => $member_data['first_name'],
                "last_name" => $member_data['last_name'],
                "email" => $member_data['email'],
            );
            $replace = array(
                "unsubscribe" => $this->config->get('config_url') . 'ext/unsubcribe?sid=' . $this->send_id . '&mid=' . $member_data['subscriber_id'] . '&hash=' . md5("Unsub " . $member_data['subscriber_id'] . "from " . $this->send_id),
                "view_online" => $this->config->get('config_url') . 'ext/view?id=' . $send_data['newsletter_id'] . '&sid=' . $this->send_id . '&mid=' . $member_data['subscriber_id'] . '&hash=' . md5("view link " . $member_data['subscriber_id'] . "from " . $this->send_id),
            );
        } else {
            // for public things like share on facebook
            $replace = array(
                "email_subject" => $newsletter_data['subject'],
                "from_name" => $newsletter_data['from_name'],
                "from_email" => $newsletter_data['from_email'],
                "to_email" => $newsletter_data['from_email'],
                "sent_date" => date("jS M, Y"), // TODO - read newsletter date
                "sent_month" => date("M Y"), // TODO - read newsletter date
                "member_id" => '',
                "send_id" => '',
                "MEMBER_HASH" => '',
                "first_name" => 'Member',
                "last_name" => '',
                "email" => '',
            );

            $replace = array(
                "unsubscribe" => '#NOGO',
                "view_online" => '#NOGO'
            );
        }



        foreach ($replace as $key => $val) {
            $newsletter_html = preg_replace('/\{' . strtoupper(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
            $newsletter_html = preg_replace('/' . strtoupper(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
            $newsletter_html = preg_replace('/\{' . strtolower(preg_quote($key, '/')) . '\}/', $val, $newsletter_html);
            $newsletter_html = preg_replace('/' . strtolower(preg_quote("*|" . $key . "|*", '/')) . '/', $val, $newsletter_html);
        }
        $this->response->setOutput($newsletter_html);
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

}
