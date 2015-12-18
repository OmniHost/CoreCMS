<?php

class ControllerModuleMenu extends \Core\Controller {

    public function index($setting) {

        $this->load->model('design/menu');
        $this->data['menu_items'] = $this->model_design_menu->getMenu($setting['menu_group_id']);
        $this->data['setting'] = $setting;
        $this->template = 'module/menu.phtml';
        if(!empty($setting['override_template'])){
            $this->template = $setting['override_template'];
        }
        
        
        return $this->render();
    }

}
