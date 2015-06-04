<?php

class ControllerCommonHeader extends \Core\Controller {

    public function index() {

        $this->data['title'] = ($this->document->getTitle()) ? $this->document->getTitle() : $this->config->get('config_meta_title');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        $this->data['description'] = ($this->document->getDescription()) ? $this->document->getDescription() : $this->config->get('config_meta_description');

        $this->data['keywords'] = ($this->document->getKeywords()) ? $this->document->getKeywords() : $this->config->get('config_meta_keyword');



        if ($this->config->get('config_icon')) {

            $this->load->model('tool/image');
            $this->document->addLink($this->model_tool_image->resizeExact($this->config->get('config_icon'), 32, 32), 'shortcut icon');
        }
        $this->data['links'] = $this->document->getLinks();


        //Header menu
        $this->load->model('design/menu');
        $this->data['menu_items'] = $this->model_design_menu->getHeaderMenu();
        $this->data['home'] = $this->url->link('common/home');
        $this->data['site_name'] = $this->config->get('config_name');
        $this->load->model('tool/image');
        $this->data['config_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 0, 50);


        $this->template = 'common/header.phtml';
        $this->render();
    }

}
