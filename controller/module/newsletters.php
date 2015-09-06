<?php

class ControllerModuleNewsletters extends \Core\Controller {

    public function index() {
        $this->load->language('module/newsletter');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['error_email'] = $this->language->get('error_email');
        
        $this->data = $data;
        $this->template = 'module/newsletters.phtml';
        return $this->render();
    }

    public function subscribe() {
        $this->load->model('module/newsletters');
        
        $this->load->language('module/newsletter');

        $json = array();
        
        
        if($this->request->post['optin'] == '1'){
            $this->model_module_newsletters->subscribe($this->request->post);
            $json['message'] = $this->language->get('text_subscribed');
        }else{
            $this->model_module_newsletters->unsubscribe($this->request->post);
            $json['message'] = $this->language->get('text_unsubscribed');
        }
        
        

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
