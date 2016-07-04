<?php

class ModelBlogPost extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'blog.post';
    public $blurb;
    public $content;
    public $categories;
    public $featured_image;
    public $publish_date;

    public $relatedposts;
    
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

        $galleries = array();
        $list = json_decode($data['content'], 1);
        if ($list) {
            $galleries = $this->_linkupgalleries($list);
        }
        return $galleries;
    }

    protected function _linkupdownloads($list = array()) {
        $downloads = array();
        registry('load')->model('cms/download');
        foreach ($list as $download_id) {
            $download = registry('model_cms_download')->getDownload($download_id);
            if ($download) {
                $download['href'] = registry('url')->link('cms/download','ref_id=' . $this->id . '&download_id=' . $download_id);
                $downloads[] = $download;
            }
        }

        return $downloads;
    }
    
     protected function _linkupgalleries($list = array()) {
        $galleries = array();
        registry('load')->model('cms/banner');
        registry('load')->model('tool/image');
        foreach ($list as $banner_id) {
            $gallery = registry('model_cms_banner')->getBannerDetail($banner_id);
            if ($gallery) {
                        
            $gallery['thumb'] = registry('model_tool_image')->resizeCrop($gallery['image'], registry('config')->get('config_image_thumb_width'), registry('config')->get('config_image_thumb_height'));
        
             //   $download['href'] = registry('url')->link('cms/download','ref_id=' . $this->id . '&download_id=' . $download_id);
                $galleries[] = $gallery;
            }
        }

        return $galleries;
    }
    
    
    protected function _setcategories($data) {
        $return = array();
        
        $categories = json_decode($data['content']);
    if($categories){
        foreach($categories as $category){
            $category = $this->loadParent($category)->toArray();
            $category['href'] = \Core\Registry::getInstance()->get('url')->link('blog/category', 'ams_page_id=' . $category['id']);
            $return[] = $category;
        }
    }
        return $return;
        
    }

    

    
    
}
