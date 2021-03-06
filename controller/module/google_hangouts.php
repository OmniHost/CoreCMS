<?php

class ControllerModuleGoogleHangouts extends \Core\Controller {

    public function index() {
        $this->load->language('module/google_hangouts');

        $data['heading_title'] = $this->language->get('heading_title');

        if ($this->request->server['HTTPS']) {
            $data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('google_hangouts_code')));
        } else {
            $data['code'] = html_entity_decode($this->config->get('google_hangouts_code'));
        }

        $this->data = $data;
        $this->template = 'module/google_hangouts.phtml';
        return $this->render();
    }

}
