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
