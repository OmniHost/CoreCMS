<?php

namespace Core;

class ModulePosition extends \Core\Model {

    private $route = 'common/home';
    private $layout_id = 0;
    private $init = false;

    public function init() {
        if (NS == 'front' && !$this->init) {
            if (isset($this->request->get['p'])) {
                $this->route = (string) $this->request->get['p'];
            } else {
                $this->route = 'common/home';
            }
        
            $this->load->model('extension/module');
            $this->load->model('design/layout');
            if (isset($this->request->get['ams_page_id'])) {
                $this->layout_id = $this->model_design_layout->getAmsLayout($this->request->get['ams_page_id']);
            } else {
                $this->layout_id = $this->model_design_layout->getLayout($this->route);
            }
            if (!$this->layout_id) {
                $this->layout_id = $this->config->get('config_layout_id');
            }
            $this->init = true;
        }
    }

    public function has($layout_position = '') {
        $this->init();
        $layout_position = preg_replace("/[^a-z0-9\/\s-_. ]/i", " ", $layout_position);
        if (!$layout_position) {
            return false;
        }
        $modules = $this->model_design_layout->getLayoutModules($this->layout_id, $layout_position);
        if (!$modules) {
            return false;
        }

        foreach ($modules as $module) {
            $part = explode('.', $module['code']);


            if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
                return true;
            }

            if (isset($part[1])) {
                $setting_info = $this->model_extension_module->getModule($part[1]);

                if ($setting_info && $setting_info['status']) {
                    return true;
                }
            }
        }
    }

    public function get($layout_position = '') {
        $this->init();
        if (!$this->has($layout_position)) {
            return;
        }
        $layout_position = preg_replace("/[^a-z0-9\/\s-_. ]/i", " ", $layout_position);
        $modules = $this->model_design_layout->getLayoutModules($this->layout_id, $layout_position);
        $data = array();

        foreach ($modules as $module) {
            $part = explode('.', $module['code']);


            if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
                $data[] = $this->load->controller('module/' . $part[0]);
            }

            if (isset($part[1])) {
                $setting_info = $this->model_extension_module->getModule($part[1]);

                if ($setting_info && $setting_info['status']) {
                    $data[] = $this->load->controller('module/' . $part[0], $setting_info);
                }
            }
        }

        return $data;
    }

}
