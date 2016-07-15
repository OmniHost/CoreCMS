<?php
/**
 * @todo - Should start tracking additional metrics
 * @todo - such as referrer, time, pages visited etc going forward;;;; V2 ?
 */
class ControllerCommonHome extends \Core\Controller {

    public function index() {

        
        $this->document->addLink($this->url->link('common/home'), 'canonical');

        $this->template = "common/home.phtml";
        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function marketing() {
        if (isset($this->request->get['tracking'])) {
            setcookie('tracking', $this->request->get['tracking'], time() + 3600 * 24 * 1000, '/');
            $this->db->query("UPDATE `#__marketing` SET clicks = (clicks + 1) WHERE code = '" . $this->db->escape($this->request->get['tracking']) . "'");
        }
    }
    
    public function redirect(){
        $this->load->model('module/redirect');
        $this->model_module_redirect->detect301Status();
    }

}
