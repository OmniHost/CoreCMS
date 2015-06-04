<?php

class ControllerModuleHTML extends \Core\Controller {

    public function index($setting) {

        $this->data['heading_title'] = html_entity_decode($setting['title'], ENT_QUOTES, 'UTF-8');
        $this->data['html'] = html_entity_decode($setting['module_description'], ENT_QUOTES, 'UTF-8');
        
        $this->template = 'module/html.phtml';
        return $this->render();
    }

}
