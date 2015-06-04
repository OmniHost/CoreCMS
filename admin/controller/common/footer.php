<?php

class ControllerCommonFooter extends \Core\Controller {

    public function index() {
        $this->data['scripts'] = $this->document->getScripts();
        $this->document->resetScripts();
        $this->template = 'common/footer.phtml';
        $this->data['token'] = $this->session->data['token'];
        $this->render();
    }

}
