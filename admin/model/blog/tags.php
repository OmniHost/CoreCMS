<?php

class ModelBlogTags extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'blog.tags';
    public $public = 0;
    
    
    public function findByName($name){
        $query = $this->_db->query("select ams_page_id from #__ams_pages where namespace='blog.tags' and name like '" . $this->_db->escape(trim($name)) . "'");
        return (!empty($query->row['ams_page_id']))?$query->row['ams_page_id']: 0;
    }

}
