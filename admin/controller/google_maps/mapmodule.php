<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Google Maps - Maps Listings
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerGoogleMapsMapModule extends \Core\Controller {

    private $error = array();

    public function index() {
        //--Loading current active language file
        $this->load->language('google_maps/mapmodule');

        //--Load Helper
        $this->load->helper('google_maps');





        $this->load->model('extension/module');
        //--Check form post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_extension_module->addModule('google_maps', $this->request->post);
            } else {
                $this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL'));
        }


        //--Assign translation to $data array
        $data = array(
            'heading_title' => $this->language->get('heading_title'),
            'text_about_title' => $this->language->get('text_about_title'),
            'text_title' => !isset($this->request->get['module_id']) ? $this->language->get('text_title') : $this->language->get('text_title_edit'),
            'text_enabled' => $this->language->get('text_enabled'),
            'text_disabled' => $this->language->get('text_disabled'),
            'text_select_all' => $this->language->get('text_select_all'),
            'text_unselect_all' => $this->language->get('text_unselect_all'),
            'entry_name' => $this->language->get('entry_name'),
            'entry_ids' => $this->language->get('entry_ids'),
            'entry_width' => $this->language->get('entry_width'),
            'entry_height' => $this->language->get('entry_height'),
            'entry_zoom' => $this->language->get('entry_zoom'),
            'entry_maptype' => $this->language->get('entry_maptype'),
            'entry_status' => $this->language->get('entry_status'),
            'placeholder_ids' => $this->language->get('placeholder_ids'),
            'placeholder_width' => $this->language->get('placeholder_width'),
            'placeholder_height' => $this->language->get('placeholder_height'),
            'button_save' => $this->language->get('button_save'),
            'button_cancel' => $this->language->get('button_cancel'),
            'button_module_add' => $this->language->get('button_module_add'),
            'button_remove' => $this->language->get('button_remove'),
        );


        //--Document Scripts and Styles
        $this->document->setTitle($data['heading_title']);
        


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['ids'])) {
            $data['error_ids'] = $this->error['ids'];
        } else {
            $data['error_ids'] = '';
        }

        if (isset($this->error['width'])) {
            $data['error_width'] = $this->error['width'];
        } else {
            $data['error_width'] = '';
        }

        if (isset($this->error['height'])) {
            $data['error_height'] = $this->error['height'];
        } else {
            $data['error_height'] = '';
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
            'text' => !isset($this->request->get['module_id']) ? $this->language->get('text_title') : $this->language->get('text_title_edit'),
            'href' => $this->url->link('google_maps/mapmodule', 'token=' . $this->session->data['token'] . (!isset($this->request->get['module_id']) ? '' : '&module_id=' . $this->request->get['module_id']), 'SSL')
        );
        //--


        $data['action'] = $this->url->link('google_maps/mapmodule', 'token=' . $this->session->data['token'] . (!isset($this->request->get['module_id']) ? '' : '&module_id=' . $this->request->get['module_id']), 'SSL');
        $data['cancel'] = $this->url->link('module/google_maps', 'token=' . $this->session->data['token'], 'SSL');



        //--Maps
        $data['gmaps'] = array();
        if (isset($this->request->post['google_maps_module_map'])) {
            $data['gmaps'] = $this->request->post['google_maps_module_map'];
        } elseif ($this->config->has('google_maps_module_map')) {
            $data['gmaps'] = $this->config->get('google_maps_module_map');
        }
        //--


        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
        }


        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['ids'])) {
            $data['ids'] = $this->request->post['ids'];
        } elseif (!empty($module_info)) {
            $data['ids'] = $module_info['ids'];
        } else {
            $data['ids'] = '';
        }

        if (isset($this->request->post['width'])) {
            $data['width'] = $this->request->post['width'];
        } elseif (!empty($module_info)) {
            $data['width'] = $module_info['width'];
        } else {
            $data['width'] = '';
        }

        if (isset($this->request->post['height'])) {
            $data['height'] = $this->request->post['height'];
        } elseif (!empty($module_info)) {
            $data['height'] = $module_info['height'];
        } else {
            $data['height'] = '';
        }

        if (isset($this->request->post['zoom'])) {
            $data['zoom'] = $this->request->post['zoom'];
        } elseif (!empty($module_info)) {
            $data['zoom'] = $module_info['zoom'];
        } else {
            $data['zoom'] = '';
        }

        if (isset($this->request->post['maptype'])) {
            $data['maptype'] = $this->request->post['maptype'];
        } elseif (!empty($module_info)) {
            $data['maptype'] = $module_info['maptype'];
        } else {
            $data['maptype'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }


        $data['token'] = $this->session->data['token'];
        $this->children = array('common/header', 'common/footer');
        $this->template = 'google_maps/mapmodule.phtml';
        $this->data = $data;
        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'google_maps/mapmodule')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!isset($this->request->post['ids'])) {
            $this->error['ids'] = $this->language->get('error_ids');
        }

        if (!$this->request->post['width']) {
            $this->error['width'] = $this->language->get('error_width');
        }

        if (!$this->request->post['height']) {
            $this->error['height'] = $this->language->get('error_height');
        }
        return !$this->error;
    }

}
