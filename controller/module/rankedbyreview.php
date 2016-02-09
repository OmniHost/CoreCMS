<?php

class ControllerModuleRankedByReview extends \Core\Controller {

    public function index($setting) {



        static $module = 0;
        $this->data['module'] = $module++;

        $this->data['heading_title'] = html_entity_decode($setting['title'], ENT_QUOTES, 'UTF-8');
        $this->data['widget'] = $setting['widget'];
        $this->data['business_id'] = $setting['business_id'];

        $this->template = 'module/rankedbyreview.phtml';
        
        if($setting['widget'] == 'review'){
            $this->document->addScript('//business.rankedbyreview.com/rest/get-business-reviews/' . $setting['business_id'] . '?format=script');
        }else{
            $this->document->addScript('//business.rankedbyreview.com/rest/small-business-widget/newsletter/' . $setting['business_id'] . '.js');
        }
        $this->setOverride($setting['name']);
        return $this->render();
    }

}
