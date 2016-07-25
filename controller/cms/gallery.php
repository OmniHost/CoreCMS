<?php

class ControllerCmsGallery extends \Core\Controller\Page {
    
    protected $_namespace = 'cms/page';

    public function index() {

        //  $gallery_id = !empty($this->request->get['gallery_id'])?(int)$this->request->get['gallery_id']:0;
        //   $ams_page_id = !empty($this->request->get['ref_id'])?(int)$this->request->get['ref_id']:0;

        $this->load->model('setting/rights');
        $this->load->model('cms/banner');
        $this->load->model('tool/image');

        
         $gallery_id = 0;
         if (isset($this->request->get['gallery_id'])){
             $gallery_id = $this->request->get['gallery_id'];
         }
         if(isset($this->request->get['ref_id'])){
             if(!$this->model_setting_rights->getRight($this->request->get['ref_id'], 'ams_page')){
                 return $this->not_allowed();
             }
         }
        
  /*      if (isset($this->request->get['gallery_id']) && isset($this->request->get['ref_id']) && $this->model_setting_rights->getRight($this->request->get['ref_id'], 'ams_page')) {
            $gallery_id = $this->request->get['gallery_id'];
        } elseif (isset($this->request->get['gallery_id']) && isset($this->request->get['cmslink']) && md5(strrev($this->request->get['gallery_id']) . 'gal') == $this->request->get['cmslink']) {
            $gallery_id = $this->request->get['gallery_id'];
        } else {
            $gallery_id = 0;
        }*/

        $gallery = $this->model_cms_banner->getBannerDetail($gallery_id);
        if (!$gallery) {
            return $this->not_found('Gallery Not Found');
        }

        $this->data['gallery'] = $gallery;
        
        //ok lets build the page :-)


        $this->document->setTitle(strip_tags($gallery['name']));



        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        
        
        if(BASE_REQUEST_TYPE == 'ajax'){
            
            $this->template = 'cms/gallery_ajax.phtml';

        $theme = $this->config->get('config_template');
        $override = 'cms/gallery/' .$gallery['banner_id']. '_ajax.phtml';

        if (is_file(DIR_TEMPLATE . $theme . '/' . $override)) {
            $this->template = $override;
        }
            
        }else{
        

        $this->template = 'cms/gallery.phtml';

        $theme = $this->config->get('config_template');
        $override = 'cms/gallery/' .$gallery['banner_id']. '.phtml';

        if (is_file(DIR_TEMPLATE . $theme . '/' . $override)) {
            $this->template = $override;
        }



        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        }
                $this->response->setOutput($this->render());
    }

}
