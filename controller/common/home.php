<?php

class ControllerCommonHome extends \Core\Controller {

    public function index() {

        $this->document->setTitle($this->config->get('config_name'));
        $this->document->addLink($this->url->link('common/home'), 'canonical');
        
        $this->template = "common/home.phtml";
        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        
        $this->response->setOutput($this->render());
    }

}
