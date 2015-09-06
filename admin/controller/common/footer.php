<?php

class ControllerCommonFooter extends \Core\Controller {

    public function index() {

        $this->template = 'common/footer.phtml';
        $this->data['token'] = $this->session->data['token'];
        $this->render();
    }

}
