<?php

class ModelCmsComment extends \Core\Model {

    public function countComments($ams_page_id) {
        $query = $this->db->query("select count(*) as total from #__comments where ams_page_id = '" . (int) $ams_page_id . "' and status='1'");
        return $query->row['total'];
    }

    public function getComments($page_id, $start = 0, $limit = 20) {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        $query = $this->db->query("SELECT r.comment_id, r.author, r.rating, r.text, i.ams_page_id, i.name, r.date_added FROM #__comments r LEFT JOIN #__ams_pages i ON (r.ams_page_id = i.ams_page_id)  WHERE i.ams_page_id = '" . (int) $page_id . "' AND i.status = '1' AND r.status = '1'  ORDER BY r.date_added DESC LIMIT " . (int) $start . "," . (int) $limit);

        return $query->rows;
    }

    public function addComment($ams_page_id, $data) {
        //Akismet to get plugged in here! :-)
        
        
        $this->db->query("INSERT INTO #__comments SET author = '" . $this->db->escape($data['name']) . "', "
                . "customer_id = '" . (int) $this->customer->getId() . "', ams_page_id = '" . (int) $ams_page_id . "',"
                . " text = '" . $this->db->escape($data['text']) . "', "
                . "status = '" . (int) $this->config->get('config_comment_auto_approve') . "', date_added = NOW()");

        $comment_id = $this->db->getLastId();

        if ($this->config->get('config_review_mail')) {
            $this->language->load('mail/comment');

            $this->load->model('cms/page');
            $page_item = $this->model_cms_page->loadParent($ams_page_id);

            $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

            $message = $this->language->get('text_waiting') . "\n";
            $message .= sprintf($this->language->get('text_item'), $this->url->link($page_item->getNamespace(), 'ams_page_id=' . $ams_page_id), $this->db->escape(strip_tags($page_item->name))) . "\n";
            $message .= sprintf($this->language->get('text_reviewer'), $this->db->escape(strip_tags($data['name']))) . "\n";
            $message .= $this->language->get('text_review') . "\n";
            $message .= $this->db->escape(strip_tags($data['text'])) . "\n\n";

            $mail = new \Core\Mail($this->config->get('config_mail'));
            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject($subject);
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            // Send to additional alert emails
            $emails = explode(',', $this->config->get('config_mail_alert'));

            foreach ($emails as $email) {
                if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
                    $mail->setTo($email);
                    $mail->send();
                }
            }
        }
    }

}
