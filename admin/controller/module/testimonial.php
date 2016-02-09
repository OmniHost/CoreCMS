<?php

class ControllerModuleTestimonial extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('testimonial', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getModule();
    }

    public function insert() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateForm())) {
            $this->model_extension_testimonial->addTestimonial($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateForm())) {
            $this->model_extension_testimonial->editTestimonial($this->request->get['testimonial_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        if ((isset($this->request->post['selected'])) && ($this->validateDelete())) {
            foreach ($this->request->post['selected'] as $testimonial_id) {
                $this->model_extension_testimonial->deleteTestimonial($testimonial_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function listing() {
        $this->load->language('module/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getList();
    }

    public function getModule() {
        $this->load->language('module/testimonial');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $this->data['text_column_left'] = $this->language->get('text_column_left');
        $this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['text_pixels'] = $this->language->get('text_pixels');

        $this->data['entry_limit'] = $this->language->get('entry_limit');
        $this->data['entry_image'] = $this->language->get('entry_image');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_position'] = $this->language->get('entry_position');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_word_limit'] = $this->language->get('entry_word_limit');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_excerpt_module'] = $this->language->get('entry_excerpt_module');
        $this->data['entry_excerpt_page'] = $this->language->get('entry_excerpt_page');
        $this->data['entry_module_photo'] = $this->language->get('entry_module_photo');
        $this->data['entry_excerpt_words'] = $this->language->get('entry_excerpt_words');
        $this->data['entry_display_photo'] = $this->language->get('entry_display_photo');
        $this->data['entry_photo_size'] = $this->language->get('entry_photo_size');
        $this->data['entry_display_rating'] = $this->language->get('entry_display_rating');
        $this->data['entry_module_rating'] = $this->language->get('entry_module_rating');
        $this->data['entry_pagination_limit'] = $this->language->get('entry_pagination_limit');
        $this->data['entry_auto_approve'] = $this->language->get('entry_auto_approve');
        $this->data['entry_guest_status'] = $this->language->get('entry_guest_status');

        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_data'] = $this->language->get('tab_data');

        $this->data['button_testimonials'] = $this->language->get('button_testimonials');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_module'] = $this->language->get('button_add_module');
        $this->data['button_remove'] = $this->language->get('button_remove');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $this->data['error_image'] = $this->error['image'];
        } else {
            $this->data['error_image'] = array();
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_module'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['testimonials'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action'] = $this->url->link('module/testimonial', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['modules'] = array();

        if (isset($this->request->post['testimonial_module'])) {
            $this->data['modules'] = $this->request->post['testimonial_module'];
        } elseif ($this->config->get('testimonial_module')) {
            $this->data['modules'] = $this->config->get('testimonial_module');
        }

        $this->load->model('design/layout');

        $this->data['layouts'] = $this->model_design_layout->getLayouts();

        if (isset($this->request->post['testimonial_excerpt_module'])) {
            $this->data['testimonial_excerpt_module'] = $this->request->post['testimonial_excerpt_module'];
        } else {
            $this->data['testimonial_excerpt_module'] = $this->config->get('testimonial_excerpt_module');
        }

        if (isset($this->request->post['testimonial_excerpt_page'])) {
            $this->data['testimonial_excerpt_page'] = $this->request->post['testimonial_excerpt_page'];
        } else {
            $this->data['testimonial_excerpt_page'] = $this->config->get('testimonial_excerpt_page');
        }

        if (isset($this->request->post['testimonial_module_photo'])) {
            $this->data['testimonial_module_photo'] = $this->request->post['testimonial_module_photo'];
        } else {
            $this->data['testimonial_module_photo'] = $this->config->get('testimonial_module_photo');
        }

        if (isset($this->request->post['testimonial_words'])) {
            $this->data['testimonial_words'] = $this->request->post['testimonial_words'];
        } else {
            $this->data['testimonial_words'] = $this->config->get('testimonial_words');
        }

        if (isset($this->request->post['testimonial_display_photo'])) {
            $this->data['testimonial_display_photo'] = $this->request->post['testimonial_display_photo'];
        } else {
            $this->data['testimonial_display_photo'] = $this->config->get('testimonial_display_photo');
        }

        if (isset($this->request->post['testimonial_photo_size'])) {
            $this->data['testimonial_photo_size'] = $this->request->post['testimonial_photo_size'];
        } else {
            $this->data['testimonial_photo_size'] = $this->config->get('testimonial_photo_size');
        }

        if (isset($this->request->post['testimonial_display_rating'])) {
            $this->data['testimonial_display_rating'] = $this->request->post['testimonial_display_rating'];
        } else {
            $this->data['testimonial_display_rating'] = $this->config->get('testimonial_display_rating');
        }

        if (isset($this->request->post['testimonial_module_rating'])) {
            $this->data['testimonial_module_rating'] = $this->request->post['testimonial_module_rating'];
        } else {
            $this->data['testimonial_module_rating'] = $this->config->get('testimonial_module_rating');
        }

        if (isset($this->request->post['testimonial_pagination_limit'])) {
            $this->data['testimonial_pagination_limit'] = $this->request->post['testimonial_pagination_limit'];
        } else {
            $this->data['testimonial_pagination_limit'] = $this->config->get('testimonial_pagination_limit');
        }

        if (isset($this->request->post['testimonial_auto_approve'])) {
            $this->data['testimonial_auto_approve'] = $this->request->post['testimonial_auto_approve'];
        } else {
            $this->data['testimonial_auto_approve'] = $this->config->get('testimonial_auto_approve');
        }

        if (isset($this->request->post['testimonial_guest_status'])) {
            $this->data['testimonial_guest_status'] = $this->request->post['testimonial_guest_status'];
        } else {
            $this->data['testimonial_guest_status'] = $this->config->get('testimonial_guest_status');
        }

        $this->template = 'module/testimonial.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'date_added';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_firstname'] = $this->language->get('column_firstname');
        $this->data['column_lastname'] = $this->language->get('column_lastname');
        $this->data['column_email'] = $this->language->get('column_email');
        $this->data['column_website'] = $this->language->get('column_website');
        $this->data['column_rating'] = $this->language->get('column_rating');
        $this->data['column_featured'] = $this->language->get('column_featured');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_sort_order'] = $this->language->get('column_sort_order');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_module'] = $this->language->get('button_module');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
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

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['module'] = $this->url->link('module/testimonial', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['insert'] = $this->url->link('module/testimonial/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('module/testimonial/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['testimonials'] = array();

        $data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );

        $testimonial_total = $this->model_extension_testimonial->getTotalTestimonials();

        $results = $this->model_extension_testimonial->getTestimonials($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('module/testimonial/update', 'token=' . $this->session->data['token'] . '&testimonial_id=' . $result['testimonial_id'] . $url, 'SSL')
            );

            $this->data['testimonials'][] = array(
                'testimonial_id' => $result['testimonial_id'],
                'firstname' => $result['firstname'],
                'lastname' => $result['lastname'],
                'email' => $result['email'],
                'website' => $result['website'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'rating' => $result['rating'],
                'featured' => ($result['featured'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
                'selected' => isset($this->request->post['selected']) && in_array($result['testimonial_id'], $this->request->post['selected']),
                'sort_order' => $result['sort_order'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'action' => $action
            );
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

        $this->data['sort_firstname'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=firstname' . $url, 'SSL');
        $this->data['sort_lastname'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=lastname' . $url, 'SSL');
        $this->data['sort_email'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=email' . $url, 'SSL');
        $this->data['sort_website'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=website' . $url, 'SSL');
        $this->data['sort_rating'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=rating' . $url, 'SSL');
        $this->data['sort_featured'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=featured' . $url, 'SSL');
        $this->data['sort_sort_order'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
        $this->data['sort_status'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $testimonial_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->template = 'module/testimonials/list.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());

    }

    private function getForm() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_default'] = $this->language->get('text_default');
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_good'] = $this->language->get('text_good');
        $this->data['text_bad'] = $this->language->get('text_bad');

        $this->data['entry_testimonial_id'] = $this->language->get('entry_testimonial_id');
        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_website'] = $this->language->get('entry_website');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_rating'] = $this->language->get('entry_rating');
        $this->data['entry_language'] = $this->language->get('entry_language');
        $this->data['entry_featured'] = $this->language->get('entry_featured');
        $this->data['entry_store'] = $this->language->get('entry_store');
        $this->data['entry_testimony'] = $this->language->get('entry_testimony');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_photo'] = $this->language->get('entry_photo');
        $this->data['entry_date_added'] = $this->language->get('entry_date_added');
        $this->data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['firstname'])) {
            $this->data['error_firstname'] = $this->error['firstname'];
        } else {
            $this->data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $this->data['error_lastname'] = $this->error['lastname'];
        } else {
            $this->data['error_lastname'] = '';
        }

        if (isset($this->error['email'])) {
            $this->data['error_email'] = $this->error['email'];
        } else {
            $this->data['error_email'] = '';
        }

        if (isset($this->error['rating'])) {
            $this->data['error_rating'] = $this->error['rating'];
        } else {
            $this->data['error_rating'] = '';
        }

        if (isset($this->error['testimony'])) {
            $this->data['error_testimony'] = $this->error['testimony'];
        } else {
            $this->data['error_testimony'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['testimonial_id'])) {
            $this->data['action'] = $this->url->link('module/testimonial/insert', 'token=' . $this->session->data['token'], 'SSL');
        } else {
            $this->data['action'] = $this->url->link('module/testimonial/update', 'token=' . $this->session->data['token'] . '&testimonial_id=' . $this->request->get['testimonial_id'], 'SSL');
        }

        $this->data['cancel'] = $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL');

        if ((isset($this->request->get['testimonial_id'])) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $testimonial_info = $this->model_extension_testimonial->getTestimonial($this->request->get['testimonial_id']);
        }

        if (isset($testimonial_info)) {
            $this->data['testimonial_id'] = $testimonial_info['testimonial_id'];
        } else {
            $this->data['testimonial_id'] = false;
        }

        if (isset($testimonial_info)) {
            $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($testimonial_info['date_added']));
        } else {
            $this->data['date_added'] = false;
        }

        if (isset($testimonial_info)) {
            $this->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($testimonial_info['date_modified']));
        } else {
            $this->data['date_modified'] = false;
        }

        if (isset($this->request->post['firstname'])) {
            $this->data['firstname'] = $this->request->post['firstname'];
        } elseif (isset($testimonial_info)) {
            $this->data['firstname'] = $testimonial_info['firstname'];
        } else {
            $this->data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $this->data['lastname'] = $this->request->post['lastname'];
        } elseif (isset($testimonial_info)) {
            $this->data['lastname'] = $testimonial_info['lastname'];
        } else {
            $this->data['lastname'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } elseif (isset($testimonial_info)) {
            $this->data['email'] = $testimonial_info['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->request->post['website'])) {
            $this->data['website'] = $this->request->post['website'];
        } elseif (isset($testimonial_info)) {
            $this->data['website'] = $testimonial_info['website'];
        } else {
            $this->data['website'] = '';
        }

        if (isset($this->request->post['company'])) {
            $this->data['company'] = $this->request->post['company'];
        } elseif (isset($testimonial_info)) {
            $this->data['company'] = $testimonial_info['company'];
        } else {
            $this->data['company'] = '';
        }

        if (isset($this->request->post['title'])) {
            $this->data['title'] = $this->request->post['title'];
        } elseif (isset($testimonial_info)) {
            $this->data['title'] = $testimonial_info['title'];
        } else {
            $this->data['title'] = '';
        }

        if (isset($this->request->post['testimony'])) {
            $this->data['testimony'] = $this->request->post['testimony'];
        } elseif (isset($testimonial_info)) {
            $this->data['testimony'] = html_entity_decode($testimonial_info['testimony']);
        } else {
            $this->data['testimony'] = '';
        }

        if (isset($this->request->post['rating'])) {
            $this->data['rating'] = $this->request->post['rating'];
        } elseif (isset($testimonial_info)) {
            $this->data['rating'] = $testimonial_info['rating'];
        } else {
            $this->data['rating'] = '';
        }

        

        if (isset($this->request->post['featured'])) {
            $this->data['featured'] = $this->request->post['featured'];
        } elseif (isset($testimonial_info)) {
            $this->data['featured'] = $testimonial_info['featured'];
        } else {
            $this->data['featured'] = '';
        }



        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (isset($testimonial_info)) {
            $this->data['status'] = $testimonial_info['status'];
        } else {
            $this->data['status'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $this->data['sort_order'] = $this->request->post['sort_order'];
        } elseif (isset($testimonial_info)) {
            $this->data['sort_order'] = $testimonial_info['sort_order'];
        } else {
            $this->data['sort_order'] = '';
        }

        if (isset($this->request->post['image'])) {
            $this->data['image'] = $this->request->post['image'];
        } elseif (isset($testimonial_info)) {
            $this->data['image'] = $testimonial_info['image'];
        } else {
            $this->data['image'] = '';
        }

        $this->load->model('tool/image');

        if (!empty($testimonial_info) && $testimonial_info['image'] && file_exists(DIR_IMAGE . $testimonial_info['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($testimonial_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->model_tool_image->resize('no_photo.jpg', 100, 100);
        }

        $this->data['no_photo'] = $this->model_tool_image->resize('no_photo.jpg', 100, 100);

        $this->template = 'module/testimonials/form.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['testimonial_module'])) {
            foreach ($this->request->post['testimonial_module'] as $key => $value) {
                if (!$value['image_width'] || !$value['image_height']) {
                    $this->error['image'][$key] = $this->language->get('error_image');
                }
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'module/testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((strlen(utf8_decode($this->request->post['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((strlen(utf8_decode($this->request->post['lastname'])) < 1) || (strlen(utf8_decode($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if(!empty($this->request->post['email'])){
        if ((strlen(utf8_decode($this->request->post['email'])) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }
        }

        if (strlen(utf8_decode($this->request->post['testimony'])) < 3) {
            $this->error['testimony'] = $this->language->get('error_testimony');
        }

        if (!isset($this->request->post['rating']) || !$this->request->post['rating']) {
            $this->error['rating'] = $this->language->get('error_rating');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'module/testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function install() {
        $create_testimonial = "CREATE TABLE IF NOT EXISTS `#__testimonial` "
                . "(`testimonial_id` int(11) NOT NULL AUTO_INCREMENT,"
                . " `customer_id` int(11) NOT NULL DEFAULT 0,"
                . " `status` int(1) NOT NULL DEFAULT '0', "
                . "`firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '', "
                . "`lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '', "
                . "`email` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '', "
                . "`website` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '', "
                . "`company` varchar(128) COLLATE utf8_bin, "
                . "`title` varchar(128) COLLATE utf8_bin, "
                . "`rating` int(1) NOT NULL DEFAULT '0', "
                . "`testimony` TEXT COLLATE utf8_bin NOT NULL,"
                . " `image` VARCHAR(255), "
                . "`featured` int(1) NOT NULL DEFAULT 0, "
                . "`sort_order` int(5) NOT NULL DEFAULT 99999,"
                . " `date_added` DATETIME NOT NULL, "
                . "`date_modified` DATETIME NOT NULL,"
                . " PRIMARY KEY (`testimonial_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        $this->db->query($create_testimonial);
        
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('module_testimonial', 'admin.module.menu', 'module/testimonial/menu');
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `#__testimonial`");
            $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('module_faq');
    }
    
    public function menu(&$menu) {
        $this->load->language('module/testimonial');
        $menu['module_testimonials'] = array(
            'icon' => 'fa-quote-left',
            'label' => $this->language->get('button_testimonials'),
            'href' => $this->url->link('module/testimonial/listing', 'token=' . $this->session->data['token'], 'SSL')
        );
    }

}
