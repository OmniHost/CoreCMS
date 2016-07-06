<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Newsletter Overview
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingNewsletterOverview extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter/overview');
        $this->document->setTitle($this->language->get('heading_title'));
      
        
        $this->load->model('marketing/newsletter');
        $this->load->model('marketing/newsletter/subscriber');
        $this->load->model('marketing/newsletter/group');
        $this->load->model('marketing/newsletter/campaign');
        $data['pending_sends'] = $this->model_marketing_newsletter->getPendingSends();
        $data['recent_sends'] = $this->model_marketing_newsletter->getRecentSends();
        
 
        $data['recent_members']  = $this->model_marketing_newsletter_subscriber->getSubscribers(array("limit" => 10));
        foreach($data['recent_members'] as &$member){
            $member = $this->model_marketing_newsletter_subscriber->getSubscriber($member['subscriber_id']);
            foreach($member['group_id'] as &$group){
                $group = $this->model_marketing_newsletter_group->getGroup($group);
            }
             foreach($member['campaign_id'] as &$campaign){
                $campaign = $this->model_marketing_newsletter_campaign->getCampaign($campaign);
            }
            $member['send_count'] = $this->model_marketing_newsletter_subscriber->getSendCount($member['subscriber_id']);
            $member['open_count'] = $this->model_marketing_newsletter_subscriber->getOpenCount($member['subscriber_id']);
            $member['bounce_count'] = $this->model_marketing_newsletter_subscriber->getBounceCount($member['subscriber_id']);
        }
        

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/overview.phtml', $data));
    }

}
