<?php

class ControllerCommonColumnLeft extends \Core\Controller {

    public function index() {
        $this->load->model('design/layout');

        if (isset($this->request->get['p'])) {
            $route = (string) $this->request->get['p'];
        } else {
            $route = 'common/home';
        }

        $layout_id = 0;

        if (isset($this->request->get['ams_page_id'])) {
            $layout_id = $this->model_design_layout->getAmsLayout($this->request->get['ams_page_id']);
        } else {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }

        $this->load->model('extension/module');

        $this->data['modules'] = array();

        $modules = $this->model_design_layout->getLayoutModules($layout_id, 'column_left');
        
        

        foreach ($modules as $module) {
            $part = explode('.', $module['code']);
           

            if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
                $this->data['modules'][] = $this->load->controller('module/' . $part[0]);
            }

            if (isset($part[1])) {
                $setting_info = $this->model_extension_module->getModule($part[1]);

                if ($setting_info && $setting_info['status']) {
                    $this->data['modules'][] = $this->load->controller('module/' . $part[0], $setting_info);
                }
            }
        }

        $this->template = 'common/column_left.phtml';
         $this->render();
    }

}
