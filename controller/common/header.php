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


        if (isset($this->request->get['p'])) {
            $this->data['route'] = (string) $this->request->get['p'];
        } else {
            $this->data['route'] = 'common/home';
        }

        if ($this->config->get('config_icon')) {

            $this->load->model('tool/image');
            $this->document->addLink($this->model_tool_image->resizeExact($this->config->get('config_icon'), 32, 32), 'shortcut icon');
        }

        // All Meta Settings!!!!!

        if ($this->config->get('config_meta_author')) {
            $this->document->addLink($this->config->get('config_meta_author'), 'author');
        }
        if ($this->config->get('config_meta_publisher')) {
            $this->document->addLink($this->config->get('config_meta_publisher'), 'publisher');
        }


        if (!$this->document->hasMeta('og:url')) {
            $this->document->addMeta('og:url', $this->url->link($this->data['route']), 'property');
            $this->document->addLink($this->url->link($this->data['route']), 'canonical');
        }

        if (!$this->document->hasMeta('og:title') && $this->config->get('config_facebook_ogtitle')) {
            $this->document->addMeta('og:title', $this->config->get('config_facebook_ogtitle'), 'property');
        }
        if (!$this->document->hasMeta('og:description') && $this->config->get('config_facebook_ogdescription')) {
            $this->document->addMeta('og:description', $this->config->get('config_facebook_ogdescription'), 'property');
        }
        if ($this->config->get('config_facebook_ogsitename')) {
            $this->document->addMeta('og:site_name', $this->config->get('config_facebook_ogsitename'), 'property');
        }
        if ($this->config->get('config_facebook_appid')) {
            $this->document->addMeta('fb:app-id', $this->config->get('config_facebook_appid'), 'property');
        }
        if (!$this->document->hasMeta('og:image') && $this->config->get('config_facebook_ogimage')) {
            $this->document->addMeta('og:image', $this->config->get('config_ssl') .'img/' . $this->config->get('config_facebook_ogimage'), 'property');
        }
        if (!$this->document->hasMeta('og:type') ) {
            $this->document->addMeta('og:type', 'website', 'property');
        }



        /*
         * config_facebook_ogdescription
         */



        $this->data['links'] = $this->document->getLinks();

        $this->data['metas'] = $this->document->getMeta();



        //Header menu
        $this->load->model('design/menu');
        $this->data['menu_items'] = $this->model_design_menu->getHeaderMenu();
        $this->data['home'] = $this->url->link('common/home');
        $this->data['site_name'] = $this->config->get('config_name');
        $this->load->model('tool/image');
        $this->data['config_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 0, 100);




        $this->template = 'common/header.phtml';
        $this->render();
        
    }

}
