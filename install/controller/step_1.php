<?php

class ControllerStep1 extends \Core\Controller {

    public function index() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->redirect($this->url->link('step_2'));
        }

        $this->data['action'] = $this->url->link('step_1');

        $this->template = 'step_1';
        $this->children = array(
            'header',
            'footer'
        );

        $this->response->setOutput($this->render());
    }

}

?>