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
        public $downloads;
    public $galleries;

    protected function _setcategories($data) {

        return json_decode($data['content'], 1);
    }

    protected function _populatecategories($data) {

        return json_encode($data['categories']);
    }

    protected function _setpublish_date($data) {
        return date($this->_language->get("date_time_format_short"), $data['content']);
    }

    protected function _populatepublish_date($data) {
        if ($data['publish_date'] > 0) {
            return strtotime($data['publish_date']);
        } else {
            return time();
        }
    }
    
    
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


         $tabs['general']['content'] = $this->_formTypeHtml('content', $this->_language->get('entry_content'));
      
  

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
        $tree = array();
        $options = array();
        if ($new) {
            $tree = $this->_createTree($new, $new[0]);
            $options = $this->_indent($tree);
        }


        $tabs['links']['categories'] = array(
            'key' => 'categories',
            'type' => 'scrollbox',
            'value' => $data['categories'],
            'options' => $options,
            'label' => $this->_language->get('entry_categories'),
            'required' => false
        );




        // publish date
        if (isset($this->request->post['publish_date'])) {
            $data['publish_date'] = $this->request->post['publish_date'];
        } elseif ($this->publish_date > 0) {
            $data['publish_date'] = DATE($this->_language->get("date_time_format_short"), strtotime($this->publish_date));
        } else {
            $data['publish_date'] = DATE($this->_language->get("date_time_format_short"));
        }

        $tabs['general_details']['publish_date'] = array(
            'key' => 'publish_date',
            'type' => 'datetime',
            'value' => $data['publish_date'],
            'label' => $this->_language->get('entry_publish_date'),
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


      $tabs['links']['downloads'] = $this->_formTypeAutocompleteList('downloads', $this->_language->get('entry_downloads'), registry('url')->link('cms/download/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'), false, array($this, '_linkupdownloads'));
        $tabs['links']['galleries'] = $this->_formTypeAutocompleteList('galleries', $this->_language->get('Galleries'), registry('url')->link('cms/banner/autocomplete', 'token=' . registry('session')->data['token'], 'SSL'), false, array($this, '_linkupgalleries'));
       


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
