<?php

class ModelCmsPage extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'cms.page';
    public $content;
    public $downloads;

    protected function _setdownloads($data) {

        $downloads = array();
        $list = json_decode($data['content'], 1);
        if ($list) {
            $downloads = $this->_linkupdownloads($list);
        }
        return $downloads;
    }

    protected function _linkupdownloads($list = array()) {
        $downloads = array();
        registry('load')->model('cms/download');
        foreach ($list as $download_id) {
            $download = registry('model_cms_download')->getDownload($download_id);
            if ($download) {
                $downloads[] = $download;
            }
        }

        return $downloads;
    }

    protected function _populatedownloads($data) {

        return json_encode($data['downloads']);
    }

    public function getFormFields($tabs) {

        if (isset($this->request->post['content'])) {
            $data['content'] = $this->request->post['content'];
        } elseif (!empty($this->content)) {
            $data['content'] = $this->content;
        } else {
            $data['content'] = '';
        }

        $tabs['general']['content'] = array(
            'key' => 'content',
            'type' => 'html',
            'value' => $data['content'],
            'label' => $this->_language->get('entry_content'),
            'required' => false
        );

        if (isset($this->request->post['downloads'])) {
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
            'url' => registry('url')->link('cms/download/autocomplete', 'token=' . registry(session)->data['token'], 'SSL'),
            'required' => false
        );


        return $tabs;
    }

    public function populate($array) {
        parent::populate($array);
    }

}
