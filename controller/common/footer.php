<?php

class ControllerCommonFooter extends \Core\Controller {

    public function index() {
        
        if ($this->config->get('config_google_analytics_status')) {
            $this->data['google_analytics'] = $this->config->get('config_google_analytics');
        } else {
            $this->data['google_analytics'] = '';
        }


        $this->load->model('tool/online');

        if (isset($this->request->server['REMOTE_ADDR'])) {
            $ip = $this->request->server['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
            $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
        } else {
            $url = '';
        }

        if (isset($this->request->server['HTTP_REFERER'])) {
            $referer = $this->request->server['HTTP_REFERER'];
        } else {
            $referer = '';
        }

        $this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);

         $this->load->model('tool/image');
        $this->data['config_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 0, 100);
        
           //Header menu
        $this->load->model('design/menu');
        $this->data['menu_items'] = $this->model_design_menu->getFooterMenu();


        $this->template = 'common/footer.phtml';
        $this->render();
    }

}
