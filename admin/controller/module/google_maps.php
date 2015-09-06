<?php

class ControllerModuleGoogleMaps extends \Core\Controller {

    private $error = array();

    public function index() {
        //--Check if Map Module for edit
        if (isset($this->request->get['module_id'])) {
            $this->redirect($this->url->link('google_maps/mapmodule', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL'));
        }

        //--Loading current active language file
        $this->load->language('module/google_maps');

        //--Load Helper
        $this->load->helper('google_maps');



        //--Assign translation to $data array
        $data = array(
            'heading_title' => $this->language->get('heading_title'),
            'text_about_title' => $this->language->get('text_about_title'),
            'text_list' => $this->language->get('text_list'),
            'text_add_marker' => $this->language->get('text_add_marker'),
            'text_add_module' => $this->language->get('text_add_module'),
            'text_confirm' => $this->language->get('text_confirm'),
            'column_name' => $this->language->get('column_name'),
            'column_count' => $this->language->get('column_count'),
            'column_action' => $this->language->get('column_action'),
            'column_module' => $this->language->get('column_module'),
            'button_cancel' => $this->language->get('button_cancel'),
            'button_add' => $this->language->get('button_add'),
            'button_edit' => $this->language->get('button_edit'),
            'button_close' => $this->language->get('button_close'),
            'button_delete' => $this->language->get('button_delete'),
            'permission_location' => $this->language->get('permission_location'),
            'permission_mapmodule' => $this->language->get('permission_mapmodule')
        );
        
        
        //--Document Scripts and Styles
        $this->document->setTitle($data['heading_title']);


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }


        //--Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL')
        );
        //--

        $data['wlocation'] = true;
        $data['wmapmodule'] = true;
        if (!$this->user->hasPermission('modify', 'google_maps/location') or ! $this->user->hasPermission('access', 'google_maps/location'))
            $data['wlocation'] = false;
        if (!$this->user->hasPermission('modify', 'google_maps/mapmodule') or ! $this->user->hasPermission('access', 'google_maps/mapmodule'))
            $data['wmapmodule'] = false;


        $data['action_add_marker'] = $this->url->link('google_maps/location', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_add_module'] = $this->url->link('google_maps/mapmodule', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


        //--Map Modules
        $module_data = array();
        $this->load->model('extension/module');
        $modules = $this->model_extension_module->getModulesByCode('google_maps');

        foreach ($modules as $module) {
            $module_data[] = array(
                'module_id' => $module['module_id'],
                'name' => $module['name'],
                'edit' => $this->url->link('google_maps/mapmodule', 'token=' . $this->session->data['token'] . '&module_id=' . $module['module_id'], 'SSL'),
                'delete' => $this->url->link('extension/module/delete', 'token=' . $this->session->data['token'] . '&module_id=' . $module['module_id'], 'SSL')
            );
        }
        $data['module_data'] = $module_data;
        //--
        //--Location Markers
        $data['gmaps'] = array();
        if ($this->config->has('google_maps_module_map')) {
            $data['gmaps'] = $this->config->get('google_maps_module_map');
        }
        //--






        $data['token'] = $this->session->data['token'];

        $this->children = array('common/header', 'common/footer');
        $this->template = 'module/google_maps.phtml';
        $this->data = $data;
        $this->response->setOutput($this->render());
    }

}
