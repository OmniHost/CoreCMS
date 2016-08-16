<?php

class ControllerModuleGalleries extends \Core\Controller {

    public function index($setting) {

        $this->document->addStyle('view/plugins/gallery/blueimp-gallery.min.css');
        $this->document->addStyle('view/plugins/gallery/bootstrap-image-gallery.css');
        $this->document->addScript('view/plugins/gallery/jquery.blueimp-gallery.min.js');
        $this->document->addScript('view/plugins/gallery/bootstrap-image-gallery.js');
        $this->document->addScript('view/plugins/gallery/jquery.masonry.min.js');

        static $module = 0;

        $this->load->model('cms/banner');
        $this->load->model('tool/image');
        $gallery = array();
        
        $whv = round(1400 / $setting['resize']);
        //2,3,4,6
        if($setting['resize'] == '2'){
            $data['bootcol'] = 'col-sm-6';
        }
         if($setting['resize'] == '3'){
            $data['bootcol'] = 'col-sm-4';
        }
         if($setting['resize'] == '4'){
            $data['bootcol'] = 'col-sm-3';
        }
         if($setting['resize'] == '6'){
            $data['bootcol'] = 'col-sm-2';
        }
        
        
        foreach ($setting['filter_banner_id'] as $banner_id) {
            $gallery = $this->model_cms_banner->getBannerDetail($banner_id);
            if ($gallery) {
                        
            $gallery['thumb'] = registry('model_tool_image')->resizeCrop($gallery['image'], $whv, $whv);
        
             //   $download['href'] = registry('url')->link('cms/download','ref_id=' . $this->id . '&download_id=' . $download_id);
                $galleries[] = $gallery;
            }
        }
        
       $data['galleries'] = $galleries;

        if (!empty($setting['module_description'])) {
            $data['heading_title'] = html_entity_decode($setting['module_description'], ENT_QUOTES, 'UTF-8');
        } else {

            $data['heading_title'] = '';
        }

        $data['module'] = $module++;

        $data['suffix'] = html_entity_decode($setting['class_suffix']);

        $this->data = $data;
        $this->template = 'module/galleries.phtml';
        if (!empty($setting['name'])) {
            $this->setOverride($setting['name']);
        }
        return $this->render();
    }

}
