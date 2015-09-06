<?php

class ControllerModuleGallery extends \Core\Controller {

    public function index($setting) {

        $this->document->addStyle('view/plugins/gallery/blueimp-gallery.min.css');
        $this->document->addStyle('view/plugins/gallery/bootstrap-image-gallery.css');
        $this->document->addScript('view/plugins/gallery/jquery.blueimp-gallery.min.js');
        $this->document->addScript('view/plugins/gallery/bootstrap-image-gallery.js');
        $this->document->addScript('view/plugins/gallery/jquery.masonry.min.js');

        static $module = 0;

        $this->load->model('cms/banner');
        $this->load->model('tool/image');

        $data['images'] = array();

        $results = $this->model_cms_banner->getBanners($setting);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $data['images'][] = array(
                    'title' => $result['title'],
                    'link' => $result['link'],
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                    'image' => ($setting['resize'] ? $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : $this->model_tool_image->realname($result['image']))
                );
            }
        }

        if (!empty($setting['module_description'])) {
            $data['heading_title'] = html_entity_decode($setting['module_description'], ENT_QUOTES, 'UTF-8');
        }

        $data['module'] = $module++;

        $this->data = $data;
        $this->template = 'module/gallery.phtml';
        return $this->render();
    }

}
