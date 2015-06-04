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
    
    
    protected function _setcategories($data){
        return json_decode($data['content'],1);
    }
    
    protected function _populatecategories($data){
        
        return json_encode($data['categories']);
    }
    
   
    
    public function getFormFields($tabs) {
        
      
       
        // Blurb
        if (isset($this->request->post['blurb'])) {
            $data['blurb'] = $this->request->post['blurb'];
        } elseif (!empty($this->blurb)) {
            $data['blurb'] = $this->blurb;
        } else {
            $data['blurb'] = '';
        }

        $tabs['general']['blurb'] = array(
            'key' => 'blurb',
            'type' => 'textarea',
            'value' => $data['blurb'],
            'label' => $this->_language->get('entry_blurb'),
            'required' => false
        );


        // content
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

        //categories
        if (isset($this->request->post['categories'])) {
            $data['categories'] = $this->request->post['categories'];
        } elseif (!empty($this->content)) {
            $data['categories'] = $this->categories;
        } else {
            $data['categories'] = array();
        }

        
       
        $category_model = $this->_load->model('blog/category');

        $categories = $category_model->getPages();
        $new = array();
        foreach ($categories->rows as $a) {
            $new[$a['parent_id']][] = $a;
        }
        $tree = $this->_createTree($new, $new[0]);
        $options = $this->_indent($tree);
     

        $tabs['general']['categories'] = array(
            'key' => 'categories',
            'type' => 'scrollbox',
            'value' => $data['categories'],
            'options' => $options,
            'label' => $this->_language->get('entry_categories'),
            'required' => false
        );


        if (isset($this->request->post['featured_image'])) {
            $data['featured_image'] = $this->request->post['featured_image'];
        } elseif (!empty($this->content)) {
            $data['featured_image'] = $this->featured_image;
        } else {
            $data['featured_image'] = '';
        }
        $image_model = $this->_load->model('tool/image');
        if ($data['featured_image']) {
            $thumb = $image_model->resizeExact($data['featured_image'], 100, 100);
        } else {
            $thumb = $image_model->resizeExact('no_image.jpg', 100, 100);
        }

        $tabs['general_details']['featured_image'] = array(
            'key' => 'featured_image',
            'type' => 'image',
            'placeholder' => $image_model->resizeExact('no_image.jpg', 100, 100),
            'thumb' => $thumb,
            'value' => $data['featured_image'],
            'label' => $this->_language->get('entry_featured_image'),
            'required' => false
        );




        return $tabs;
    }

    private function _createTree(&$list, $parent) {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['ams_page_id']])) {
                $l['children'] = $this->_createTree($list, $list[$l['ams_page_id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    private function _indent($arr, $level = 0, $ret = array()) {

        foreach ($arr as $opt) {

            $opt['name'] = str_repeat('-', $level) . ' ' . $opt['name'];
            $ret[] = $opt;
            if (isset($opt['children']) && count($opt['children'])) {
                $ret = $this->_indent($opt['children'], ++$level, $ret);
            }
        }
        return $ret;
    }

}
