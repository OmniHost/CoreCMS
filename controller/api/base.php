<?php

class ControllerApiBase extends \Core\Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            return $this->response->setOutput(json_encode($json));
        }
    }

}
