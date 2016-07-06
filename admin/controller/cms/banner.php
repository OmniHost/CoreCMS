<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Content - Banner / Gallery Managment
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 * 
 */
 

class ControllerCmsBanner extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('cms/banner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/banner');

        $this->getList();
    }

    public function add() {
        $this->load->language('cms/banner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/banner');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_cms_banner->addBanner($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('cms/banner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/banner');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_cms_banner->editBanner($this->request->get['banner_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('cms/banner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/banner');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $banner_id) {
                $this->model_cms_banner->deleteBanner($banner_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['insert'] = $this->url->link('cms/banner/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('cms/banner/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['banners'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $banner_total = $this->model_cms_banner->getTotalBanners();

        $results = $this->model_cms_banner->getBanners($filter_data);

        foreach ($results as $result) {
            $data['banners'][] = array(
                'banner_id' => $result['banner_id'],
                'name' => $result['name'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'edit' => $this->url->link('cms/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $result['banner_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_insert_folder'] = $this->language->get('button_insert_folder');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $banner_total;
        $pagination->page = $page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($banner_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($banner_total - $this->config->get('config_limit_admin'))) ? $banner_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $banner_total, ceil($banner_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;


        $this->template = 'cms/banner_list.phtml';
        $this->data = $data;
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['banner_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_default'] = $this->language->get('text_default');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_link'] = $this->language->get('entry_link');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_banner_add'] = $this->language->get('button_banner_add');
        $data['button_remove'] = $this->language->get('button_remove');

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

        if (isset($this->error['banner_image'])) {
            $data['error_banner_image'] = $this->error['banner_image'];
        } else {
            $data['error_banner_image'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['banner_id'])) {
            $data['action'] = $this->url->link('cms/banner/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
            $data['text_edit'] = $this->language->get('text_edit');
        } else {
            $data['action'] = $this->url->link('cms/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $this->request->get['banner_id'] . $url, 'SSL');
            $data['text_edit'] = $this->language->get('text_edit');
        }

        $data['cancel'] = $this->url->link('cms/banner', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['banner_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $banner_info = $this->model_cms_banner->getBanner($this->request->get['banner_id']);
        }

       
        $data['token'] = $this->session->data['token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($banner_info)) {
            $data['name'] = $banner_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($banner_info)) {
            $data['status'] = $banner_info['status'];
        } else {
            $data['status'] = true;
        }

        //debugPre($this->model_cms_banner->getBannerImages($this->request->get['banner_id']));
       // exit;
        $this->load->model('tool/image');

        if (isset($this->request->post['banner_image'])) {
            $banner_images = $this->request->post['banner_image'];
        } elseif (isset($this->request->get['banner_id'])) {
            $banner_images = $this->model_cms_banner->getBannerImages($this->request->get['banner_id']);
        } else {
            $banner_images = array();
        }

        $data['banner_images'] = array();

        foreach ($banner_images as $banner_image) {
            if (is_file(DIR_IMAGE . $banner_image['image'])) {
                $image = $banner_image['image'];
                $thumb = $banner_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.jpg';
            }

            $data['banner_images'][] = array(
                'title' => $banner_image['title'],
                 'description' => $banner_image['description'],
                'link' => $banner_image['link'],
                'image' => $image,
                'thumb' => $this->model_tool_image->resizeExact($thumb, 100, 100),
                'sort_order' => $banner_image['sort_order']
            );
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        $this->template = 'cms/banner_form.phtml';
        $this->data = $data;
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'cms/banner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

      
        
        if (isset($this->request->post['banner_image'])) {
            foreach ($this->request->post['banner_image'] as $banner_image_id => $banner_image) {
             
                     if (utf8_strlen($banner_image['title']) <= 1) {
                         $this->request->post['banner_image'][$banner_image_id]['title'] = utf8_substr($this->request->post['name'], 0, 64);
                         $banner_image['title'] = $this->request->post['banner_image'][$banner_image_id]['title'];
                     }
                    if (utf8_strlen($banner_image['title']) > 64) {
                        $this->request->post['banner_image'][$banner_image_id]['title'] = utf8_substr($banner_image['title'], 0, 64);
                    }
                
            }
        }
        

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'cms/banner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('cms/banner');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_cms_banner->getBanners($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'banner_id' => $result['banner_id'],
                    'id' => $result['banner_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
