<?php

class ControllerCmsPage extends \Core\Controller\Page {

    protected $_namespace = 'cms/page';

    public function index() {

        $this->getPage();
        
        if($this->_error){
            return $this->showErrorPage();
        }

        $this->data['page']['content'] = html_entity_decode($this->data['page']['content'], ENT_QUOTES, 'UTF-8');
        $this->event->trigger('ams.page.content', $this->data['page']['content']);
        $this->response->setOutput($this->render());
        
    }

}
