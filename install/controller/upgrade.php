<?php

class ControllerUpgrade extends \Core\Controller {

    private $error = array();

    public function index() {
         
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('upgrade');
     
            $this->model_upgrade->mysql();

            $this->redirect($this->url->link('upgrade/success'));
        }

        $data = array();

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action'] = $this->url->link('upgrade');
        $this->template = 'upgrade';
        $this->children = array(
            'header',
            'footer'
        );
        $this->data = $data;
        $this->response->setOutput($this->render());
    }

    public function success() {
        $data = array();

        $this->template = 'success';
        $this->children = array(
            'header',
            'footer'
        );
        $this->data = $data;
        $this->response->setOutput($this->render());
    }

    private function validate() {
        $config = $this->config;
    
        $db = new \Core\Db($config->get('DB_DRIVER'), $config->get('DB_HOSTNAME'), $config->get('DB_USERNAME'), $config->get('DB_PASSWORD'), $config->get('DB_DATABASE'), $config->get('DB_PREFIX'));

        return !$this->error;
    }

}
