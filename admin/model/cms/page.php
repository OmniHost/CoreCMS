<?php

class ModelCmsPage extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'cms.page';
    public $content;
    public $downloads;
    public $galleries;

    protected function _setdownloads($data) {

        $downloads = array();
        $list = json_decode($data['content'], 1);
        if ($list) {
            $downloads = $this->_linkupdownloads($list);
        }
        return $downloads;
    }
    
     protected function _setgalleries($data) {

        $downloads = array();
        $list = json_decode($data['content'], 1);
        if ($list) {
            $downloads = $this->_linkupgalleries($list);
        }
        return $downloads;
    }

    protected function _linkupdownloads($list = array()) {
        $downloads = array();
        registry('load')->model('cms/download');
        foreach ($list as $download_id) {
            $download = registry('model_cms_download')->getDownload($download_id);
            if ($download) {
                $download['id'] = $download_id;
                $downloads[] = $download;
            }
        }

        return $downloads;
    }
    protected function _linkupgalleries($list = array()) {
        $downloads = array();
        registry('load')->model('cms/banner');
        foreach ($list as $download_id) {
            $download = registry('model_cms_banner')->getBanner($download_id);
            if ($download) {
                $download['id'] = $download_id;
                $downloads[] = $download;
            }
        }

        return $downloads;
    }
    

    protected function _populatedownloads($data) {

        return json_encode($data['downloads']);
    }
    
     protected function _populategalleries($data) {

        return json_encode($data['galleries']);
    }

    public function getFormFields($tabs) {

        $tabs['general']['content'] = $this->_formTypeHtml('content', $this->_language->get('entry_content'));
      
        $tabs['links']['downloads'] = $this->_formTypeAutocompleteList('downloads', $this->_language->get('entry_downloads'), registry('url')->link('cms/download/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'), false, array($this, '_linkupdownloads'));
        $tabs['links']['galleries'] = $this->_formTypeAutocompleteList('galleries', $this->_language->get('Galleries'), registry('url')->link('cms/banner/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'), false, array($this, '_linkupgalleries'));
        

        
        /*if (isset($this->request->post['downloads'])) {
            $data['downloads'] = $this->_linkupdownloads($this->request->post['downloads']);
        } elseif (!empty($this->content)) {
            $data['downloads'] = $this->downloads;
        } else {
            $data['downloads'] = array();
        }

        $tabs['general']['downloads'] = array(
            'key' => 'downloads',
            'type' => 'autocomplete_list',
            'value' => $data['downloads'],
            'label' => $this->_language->get('entry_downloads'),
            'url' => registry('url')->link('cms/download/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'),
            'required' => false
        );
        
        if (isset($this->request->post['galleries'])) {
            $data['downloads'] = $this->_linkupdownloads($this->request->post['downloads']);
        } elseif (!empty($this->content)) {
            $data['downloads'] = $this->downloads;
        } else {
            $data['downloads'] = array();
        }

        $tabs['general']['downloads'] = array(
            'key' => 'downloads',
            'type' => 'autocomplete_list',
            'value' => $data['downloads'],
            'label' => $this->_language->get('entry_downloads'),
            'url' => registry('url')->link('cms/download/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'),
            'required' => false
        );*/


        return $tabs;
    }

    public function populate($array) {
        parent::populate($array);
    }

}
