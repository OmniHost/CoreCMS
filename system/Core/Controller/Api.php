<?php

namespace Core\Controller;

abstract class Api extends \Core\Controller {

    public function __construct() {
        parent::__construct();
    }

    public function testAuth() {
        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

}
