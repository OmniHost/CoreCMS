<?php

class ControllerModuleCarousel extends \Core\Controller {

    public function index($setting) {
        static $module = 0;

        $this->load->model('cms/banner');
        $this->load->model('tool/image');

        $this->document->addStyle('view/css/owl.carousel.css');
        $this->document->addScript('view/js/owl.carousel.js');

        $data['banners'] = array();

        $results = $this->model_cms_banner->getBanner($setting['banner_id']);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {

                if ($setting['width'] && $setting['height']) {
                    $image = $this->model_tool_image->resizeExact($result['image'], $setting['width'], $setting['height']);
                } else {
                    $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
                }
                $data['banners'][] = array(
                    'title' => $result['title'],
                    'link' => $result['link'],
                    'image' => $image
                );
            }
        }

        $data['module'] = $module++;

        $this->data = $data;
        $this->template = 'module/carousel.phtml';
        return $this->render();
    }

}
