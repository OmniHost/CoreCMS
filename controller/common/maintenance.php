<?php

class ControllerCommonMaintenance extends \Core\Controller {

    public function index() {
        
        
        
        if ($this->config->get('config_maintenance')) {

            $this->user = new \Core\User();
           
            if (!$this->user->isLogged()) {
                return new \Core\Action('common/maintenance/info');
            }
        }
    }

    public function info() {
        $this->load->language('common/maintenance');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
            $this->response->addHeader('HTTP/1.1 503 Service Unavailable');
        } else {
            $this->response->addHeader('HTTP/1.0 503 Service Unavailable');
        }

        $this->response->addHeader('Retry-After: 3600');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_maintenance'),
            'href' => $this->url->link('common/maintenance')
        );

        $data['message'] = $this->language->get('text_message');

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->data = $data;
      
        $this->template = 'common/maintenance.phtml';
        $this->response->setOutput($this->render());
    }

}
