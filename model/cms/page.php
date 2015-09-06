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

}
