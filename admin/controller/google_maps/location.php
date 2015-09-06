<?php

class ControllerGoogleMapsLocation extends \Core\Controller {

    private $error = array();

    public function index() {
        //--Loading current active language file
        $this->load->language('google_maps/location');

        //--Load Helper
        $this->load->helper('google_maps');


        $this->load->model('setting/setting');
        //--Check form post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('google_maps', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL'));
        }


        //--Assign translation to $data array
        $data =  array(
            'heading_title' => $this->language->get('heading_title'),
            'text_about_title' => $this->language->get('text_about_title'),
            'text_title' => $this->language->get('text_title'),
            'entry_id' => $this->language->get('entry_id'),
            'entry_alias' => $this->language->get('entry_alias'),
            'entry_address' => $this->language->get('entry_address'),
            'entry_latitude' => $this->language->get('entry_latitude'),
            'entry_longitude' => $this->language->get('entry_longitude'),
            'entry_balloon_width' => $this->language->get('entry_balloon_width'),
            'entry_ballon_text' => $this->language->get('entry_ballon_text'),
            'placeholder_id' => $this->language->get('placeholder_id'),
            'placeholder_alias' => $this->language->get('placeholder_alias'),
            'placeholder_address' => $this->language->get('placeholder_address'),
            'placeholder_latitude' => $this->language->get('placeholder_latitude'),
            'placeholder_longitude' => $this->language->get('placeholder_longitude'),
            'placeholder_balloon_width' => $this->language->get('placeholder_balloon_width'),
            'confirm_mapid' => $this->language->get('confirm_mapid'),
            'button_save' => $this->language->get('button_save'),
            'button_cancel' => $this->language->get('button_cancel'),
            'button_new_map' => $this->language->get('button_new_map'),
            'button_close' => $this->language->get('button_close')
        );


        //--Document Scripts and Styles
        $this->document->setTitle($data['heading_title']);
        $this->document->addStyle('view/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');
        $this->document->addScript('view/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js');
        $this->document->addScript('view/plugins/cnplugins/jquery.predefinedinput-1.0.1.js');
        $this->document->addScript('//maps.google.com/maps/api/js?sensor=false&libraries=places');
        $this->document->addScript('view/plugins/locationpicker/locationpicker.jquery.js');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['id'])) {
            $data['error_id'] = $this->error['id'];
        } else {
            $data['error_id'] = '';
        }

        if (isset($this->error['latitude'])) {
            $data['error_latitude'] = $this->error['latitude'];
        } else {
            $data['error_latitude'] = '';
        }

        if (isset($this->error['longitude'])) {
            $data['error_longitude'] = $this->error['longitude'];
        } else {
            $data['error_longitude'] = '';
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
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_title'),
            'href' => $this->url->link('google_maps/location', 'token=' . $this->session->data['token'], 'SSL')
        );
        //--


        $data['action'] = $this->url->link('google_maps/location', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL');



        //--Maps
        $data['gmaps'] = array();
        if (isset($this->request->post['google_maps_module_map'])) {
            $data['gmaps'] = $this->request->post['google_maps_module_map'];
        } elseif ($this->config->has('google_maps_module_map')) {
            $data['gmaps'] = $this->config->get('google_maps_module_map');
        }
        //--



        $data['token'] = $this->session->data['token'];

        $this->children = array('common/header', 'common/footer');
        $this->template = 'google_maps/location.phtml';
        $this->data = $data;
        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'google_maps/location')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }


        if (isset($this->request->post['google_maps_module_map'])) {
            foreach ($this->request->post['google_maps_module_map'] as $key => $value) {
                if (!$value['id']) {
                    $this->error['id'] = $this->language->get('error_mapid');
                }

                if (!$value['latitude']) {
                    $this->error['latitude'] = $this->language->get('error_latlong');
                }

                if (!$value['longitude']) {
                    $this->error['longitude'] = $this->language->get('error_latlong');
                }
            }
        }

        return !$this->error;
    }

}
