<?php

namespace Core\Ams;

Abstract class Base extends \Core\Controller {

    protected $_namespace;
    protected $_pageModel;
    protected $_pageInfo;
    protected $_enableComments = true;

    public function index() {

        $this->language->load('cms/page');
        $this->language->load($this->_namespace);
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getList();
    }

    public function insert() {
        $this->language->load('cms/page');
        $this->language->load($this->_namespace);
        $this->load->model('tool/seo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model($this->_namespace);
        $this->load->model('tool/seo');
        $_model = 'model_' . str_replace('/', '_', $this->_namespace);
        $this->_pageModel = \Core\Core::$registry->get($_model);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            
            
            $this->_pageModel->populate($this->request->post);
            $this->_pageModel->user_id = $this->user->getId();
            $this->_pageModel->save();
            $this->_pageModel->storeRevision($this->request->post,$this->user->getId());

            $this->model_tool_seo->setUrl('ams_page_id=' . $this->_pageModel->id, $this->request->post['slug']);

            $this->load->model('setting/rights');
            $this->model_setting_rights->setAllowedGroups($this->_pageModel->id, 'ams_page', $this->request->post['allowed_groups']);
            $this->model_setting_rights->setDeniedGroups($this->_pageModel->id, 'ams_page', $this->request->post['denied_groups']);
            //  $this->model_user_user->addUser($this->request->post);

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
            if (isset($this->request->get['update'])) {
                $this->redirect(fixajaxurl($this->url->link($this->_namespace . '/update', 'ams_page_id=' . 'ams_page_id=' . $this->_pageModel->id . '&token=' . $this->session->data['token'] . $url, 'SSL')));
            } else {
                $this->redirect(fixajaxurl($this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL')));
            }
            


            //   $this->redirect(fixajaxurl($this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL')));
        }

        $this->getForm();
    }

    public function update() {
        $this->language->load('cms/page');
        $this->language->load($this->_namespace);
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model($this->_namespace);
        $this->load->model('tool/seo');
        $_model = 'model_' . str_replace('/', '_', $this->_namespace);
        $this->_pageModel = \Core\Core::$registry->get($_model);


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            // $this->model_user_user->editUser($this->request->get['user_id'], $this->request->post);
            $this->_pageModel->loadPageObject($this->request->get['ams_page_id']);
            $this->_pageModel->storeRevision($this->request->post,$this->user->getId());
            $this->_pageModel->populate($this->request->post);
            $this->_pageModel->save();
            $this->model_tool_seo->setUrl('ams_page_id=' . $this->_pageModel->id, $this->request->post['slug']);

            $this->load->model('setting/rights');
            $this->model_setting_rights->setAllowedGroups($this->_pageModel->id, 'ams_page', $this->request->post['allowed_groups']);
            $this->model_setting_rights->setDeniedGroups($this->_pageModel->id, 'ams_page', $this->request->post['denied_groups']);

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
            if (isset($this->request->get['update'])) {
                $this->redirect(fixajaxurl($this->url->link($this->_namespace . '/update', 'ams_page_id=' . 'ams_page_id=' . $this->_pageModel->id . '&token=' . $this->session->data['token'] . $url, 'SSL')));
            } else {
                $this->redirect(fixajaxurl($this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL')));
            }
        }

        $this->getForm();
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', $this->_namespace)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (utf8_strlen($this->request->post['name']) < 1 || utf8_strlen($this->request->post['name']) > 250) {
            $this->error['name'] = $this->language->get('error_name');
        }

        $this->validateSlug();

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function getSlug() {
        $page_name = slug(trim(html_entity_decode($this->request->post['name'])));
        $this->load->model('tool/seo');
        $page_id = 0;
        if(!empty($this->request->post['page_id'])){
            $page_id = 'ams_page_id=' . $this->request->post['page_id'];
        }
        $keyword = $this->model_tool_seo->getUniqueSlug($page_name, $page_id);
        $this->response->setOutput(json_encode(array('slug' => $keyword)));
    }

    protected function validateSlug() {
        $this->load->model('tool/seo');
        $slug = $this->request->post['slug'];
        if ($slug) {

            if (isset($this->request->get['ams_page_id'])) {
                if ($slug != $this->model_tool_seo->getUrl('ams_page_id=' . (int) $this->request->get['ams_page_id'])) {
                    if ($this->model_tool_seo->hasUrl($slug)) {
                        $this->request->post['slug'] = $this->model_tool_seo->getUniqueSlug($this->request->post['name']);
                    }
                }
            } else {
                if ($this->model_tool_seo->hasUrl($slug)) {
                    $this->request->post['slug'] = $this->model_tool_seo->getUniqueSlug($this->request->post['name']);
                }
            }
        } else {
            $this->request->post['slug'] = $this->model_tool_seo->getUniqueSlug($this->request->post['name']);
        }
    }

    protected function getForm() {
        //    $this->language->load('cms/page');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_seo_url'] = $this->language->get('entry_seo_url');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_parent'] = $this->language->get('entry_parent');
        $this->data['entry_comments'] = $this->language->get('entry_comments');

        $this->data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $this->data['entry_meta_keywords'] = $this->language->get('entry_meta_keywords');
        $this->data['entry_meta_description'] = $this->language->get('entry_meta_description');


        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_meta'] = $this->language->get('tab_meta');
        $this->data['tab_permission'] = $this->language->get('tab_permission');
        $this->data['tab_revision'] = $this->language->get('tab_revision');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['site_url'] = $this->config->get('config_catalog');


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = '';
        }
        if (isset($this->error['content'])) {
            $this->data['error_content'] = $this->error['content'];
        } else {
            $this->data['error_content'] = '';
        }
        if (isset($this->error['slug'])) {
            $this->data['error_slug'] = $this->error['slug'];
        } else {
            $this->data['error_slug'] = '';
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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['ams_page_id'])) {
            $this->data['action'] = $this->url->link($this->_namespace . '/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
            $this->data['doslug'] = true;
            $this->data['autosave'] = fixajaxurl($this->url->link($this->_namespace . '/autosave', 'token=' . $this->session->data['token'] . '&_autosave_=1&ams_page_id=0', 'SSL'));
            $this->data['history'] = fixajaxurl($this->url->link($this->_namespace . '/get_revisions', 'token=' . $this->session->data['token'] . '&page=1&ams_page_id=0', 'SSL'));
        } else {
            $this->data['action'] = $this->url->link($this->_namespace . '/update', 'token=' . $this->session->data['token'] . '&ams_page_id=' . $this->request->get['ams_page_id'] . $url, 'SSL');
            $this->data['doslug'] = false;
            $this->data['autosave'] = fixajaxurl($this->url->link($this->_namespace . '/autosave', 'token=' . $this->session->data['token'] . '&_autosave_=1&ams_page_id=' . $this->request->get['ams_page_id'], 'SSL'));
            $this->data['history'] = fixajaxurl($this->url->link($this->_namespace . '/get_revisions', 'token=' . $this->session->data['token'] . '&page=1&ams_page_id='. $this->request->get['ams_page_id'], 'SSL'));
        }

        $this->data['cancel'] = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['namespace'] = $this->_namespace;


        $this->load->model($this->_namespace);
        $this->load->model('tool/seo');
        $_model = 'model_' . str_replace('/', '_', $this->_namespace);
        $this->_pageModel = \Core\Core::$registry->get($_model);

        if (isset($this->request->get['ams_page_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            
            if(isset($this->request->get['revision_id'])){
                $page_info = $this->_pageModel->loadPageRevision($this->request->get['revision_id']);
            }else{
                $page_info = $this->_pageModel->loadPageObject($this->request->get['ams_page_id'])->toArray();
            }
            $page_info['slug'] = $this->model_tool_seo->getUrl('ams_page_id=' . $page_info['id']);
            $this->_pageInfo = $page_info;
        }elseif(!isset($this->request->get['ams_page_id']) && isset($this->request->get['revision_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')){
            $page_info = $this->_pageModel->loadPageRevision($this->request->get['revision_id']);
            $this->request->post = $page_info;
        }



        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } elseif (!empty($page_info)) {
            $this->data['name'] = $page_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($this->request->post['slug'])) {
            $this->data['slug'] = $this->request->post['slug'];
        } elseif (!empty($page_info)) {
            $this->data['slug'] = $page_info['slug'];
        } else {
            $this->data['slug'] = '';
        }

        if (isset($this->request->post['parent_id'])) {
            $this->data['parent_id'] = $this->request->post['parent_id'];
        } elseif (!empty($page_info)) {
            $this->data['parent_id'] = $page_info['parent_id'];
        } else {
            $this->data['parent_id'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['parent_id'];
        } elseif (!empty($page_info)) {
            $this->data['status'] = $page_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if (isset($this->request->post['comments'])) {
            $this->data['comments'] = $this->request->post['comments'];
        } elseif (!empty($page_info)) {
            $this->data['comments'] = $page_info['comments'];
        } else {
            $this->data['comments'] = 1;
        }

        if (isset($this->request->post['meta_title'])) {
            $this->data['meta_title'] = $this->request->post['meta_title'];
        } elseif (!empty($page_info)) {
            $this->data['meta_title'] = $page_info['meta_title'];
        } else {
            $this->data['meta_title'] = '';
        }
        
        if (isset($this->request->post['meta_og_description'])) {
            $this->data['meta_og_description'] = $this->request->post['meta_og_description'];
        } elseif (!empty($page_info)) {
            $this->data['meta_og_description'] = $page_info['meta_og_description'];
        } else {
            $this->data['meta_og_description'] = '';
        }
        
        if (isset($this->request->post['meta_og_title'])) {
            $this->data['meta_og_title'] = $this->request->post['meta_og_title'];
        } elseif (!empty($page_info)) {
            $this->data['meta_og_title'] = $page_info['meta_og_title'];
        } else {
            $this->data['meta_og_title'] = '';
        }
        
        if (isset($this->request->post['meta_og_image'])) {
            $this->data['meta_og_image'] = $this->request->post['meta_og_title'];
        } elseif (!empty($page_info)) {
            $this->data['meta_og_image'] = $page_info['meta_og_image'];
        } else {
            $this->data['meta_og_image'] = '';
        }
        
        
        
         $image_model = $this->load->model('tool/image');
        if ($this->data['meta_og_image']) {
            $src_og_image = $image_model->resizeExact($this->data['meta_og_image'], 100, 100);
        } else {
            $src_og_image = $image_model->resizeExact('no_image.jpg', 100, 100);
        }
        $this->data['src_og_image'] = $src_og_image;
        $this->data['placeholder'] = $image_model->resizeExact('no_image.jpg', 100, 100);
        $this->data['meta_og_image'] = $meta_og_image;

        if (isset($this->request->post['meta_keywords'])) {
            $this->data['meta_keywords'] = $this->request->post['meta_keywords'];
        } elseif (!empty($page_info)) {
            $this->data['meta_keywords'] = $page_info['meta_keywords'];
        } else {
            $this->data['meta_keywords'] = '';
        }



        if (isset($this->request->post['meta_description'])) {
            $this->data['meta_description'] = $this->request->post['status'];
        } elseif (!empty($page_info)) {
            $this->data['meta_description'] = $page_info['meta_description'];
        } else {
            $this->data['meta_description'] = '';
        }

        if (isset($this->request->post['layout_id'])) {
            $this->data['layout_id'] = $this->request->post['layout_id'];
        } elseif (!empty($page_info)) {
            $this->data['layout_id'] = $page_info['layout_id'];
        } else {
            $this->data['layout_id'] = $this->config->get('config_layout_id');
        }


        $this->load->model('design/layout');
        $this->data['layouts'] = $this->model_design_layout->getLayouts();


        //User Groups
        $this->load->model('sale/customer_group');
        $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(array('sort' => 'cg.sort_order'));



        $this->load->model('setting/rights');

        if (isset($this->request->post['allowed_groups'])) {
            $allowed_groups = $this->request->post['allowed_groups'];
        } elseif (isset($this->request->get['ams_page_id'])) {
            $allowed_groups = $this->model_setting_rights->getAllowedGroups($this->request->get['ams_page_id'], 'ams_page'); //1 is information type
        } else {
            $allowed_groups[] = -1;
        }

        $this->data['allowed_groups'] = $allowed_groups;

        if (isset($this->request->post['denied_groups'])) {
            $denied_groups = $this->request->post['denied_groups'];
        } elseif (isset($this->request->get['ams_page_id'])) {
            $denied_groups = $this->model_setting_rights->getDeniedGroups($this->request->get['ams_page_id'], 'ams_page'); //1 is information type
        } else {
            $denied_groups = array();
        }

        $this->data['denied_groups'] = $denied_groups;


        $this->data['parents'] = $this->getPageTree();

        $this->data['ams_commentable'] = $this->_enableComments; //$_enableComments

        $tabs = array(
            'general' => array(),
            'general_details' => array(),
            'meta' => array()
        );

        $this->data['tabs'] = $this->_pageModel->getFormFields($tabs);


        
        //Autosave::
        $this->data['autosave_enabled'] = $this->config->get('config_autosave_status');
        $this->data['autosave_time'] = ($this->config->get('config_autosave_time') && (int)$this->config->get('config_autosave_time') > 30)? (int)$this->config->get('config_autosave_time') : 120;
        

        // $this->document->addScript('view/plugins/ckeditor/ckeditor.js');
        init_wysiwyg();
        $this->template = 'cms/page_form.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function getList() {
        $page = isset($this->request->get['page']) ? (int) $this->request->get['page'] : 1;
        $limit = $this->config->get('config_limit_admin');
        $filter = array();

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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );


        $this->data['insert'] = $this->url->link($this->_namespace . '/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link($this->_namespace . '/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->load->model($this->_namespace);
        $this->load->model('tool/seo');
        $_model = 'model_' . str_replace('/', '_', $this->_namespace);
        $this->_pageModel = \Core\Core::$registry->get($_model);


        $pages = $this->_pageModel->getPages($filter, $sort, $order, $page, $limit);


        $this->data['pages'] = array();
        foreach ($pages->rows as $_page) {
            $action = array();
            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link($this->_namespace . '/update', 'token=' . $this->session->data['token'] . '&ams_page_id=' . $_page['ams_page_id'] . $url, 'SSL')
            );
            $_page['action'] = $action;
            $_page['status'] = ($_page['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'));
            $_page['selected'] = (isset($this->request->post['selected']) && in_array($_page['ams_page_id'], $this->request->post['selected'])) ? '1' : false;
            $_page['date_created'] = DATE($this->language->get('date_time_format_short'), $_page['date_created']);
            $_page['date_modified'] = DATE($this->language->get('date_time_format_short'), $_page['date_modified']);
            $_page['slug'] = $this->model_tool_seo->getUrl('ams_page_id=' . $_page['ams_page_id']);
            $this->data['pages'][] = $_page;
        }

        
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }


        $pagination = new \Core\Pagination();
        $pagination->total = $pages->total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');



        $this->data['pagination'] = $pagination->render();

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
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

        $this->data['sort_name'] = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $this->data['sort_status'] = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
        $this->data['sort_date_modified'] = $this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . '&sort=date_modified' . $url, 'SSL');

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;


        $this->template = 'cms/page_list.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function delete() {
        $this->language->load($this->_namespace);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model($this->_namespace);
        $this->load->model('tool/seo');
        $_model = 'model_' . str_replace('/', '_', $this->_namespace);
        $this->_pageModel = \Core\Core::$registry->get($_model);

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $ams_page_id) {

                try {
                    $this->_pageModel->loadPageObject($ams_page_id)->delete();
                    $this->model_tool_seo->deleteUrl('ams_page_id=' . $ams_page_id);
                } catch (Core\Exception $e) {
                    //is ok.
                }
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

            $this->redirect(fixajaxurl($this->url->link($this->_namespace, 'token=' . $this->session->data['token'] . $url, 'SSL')));
        }

        $this->getList();
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', $this->_namespace)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function getPageTree() {
        $rows = $this->db->fetchAll("select ams_page_id as id,name,parent_id as parentid from #__ams_pages where namespace='" . $this->db->escape(str_replace("/", ".", $this->_namespace)) . "' order by parent_id asc, lower(name) asc");

        $new = array();
        foreach ($rows as $a) {

            $new[$a['parentid']][] = $a;
        }

        $tree = $this->_createTree($new, $new[0]); // changed
        return $tree;
    }

    private function _createTree(&$list, $parent) {
        $tree = array();


        foreach ($parent as $k => $l) {
            if (isset($list[$l['id']])) {
                $l['children'] = $this->_createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    
    
    public function autosave() {
        $id = isset($this->request->get['ams_page_id']) ? (int) $this->request->get['ams_page_id'] : '0';
        $page_data = $this->request->post;
        $autosave = isset($this->request->get['_autosave_']) ? 1 : 0;
        if (!$autosave) {
            $this->db->query("delete from #__ams_revisions where ams_page_id = '" . (int) $id . "' and autosave = '1'");
        }
        $version = 0;
        $last_pagedata = '';
        if ($id) {
            $q = $this->db->query("select * from #__ams_revisions where ams_page_id = '" . (int) $id . "' and autosave = '0' order by version desc limit 1");
            if ($q->row) {
                $version = $q->row['version'];
                $last_pagedata = $q->row['pagedata'];
            }
        }
        $version++;
        $this->response->addHeader('Content-Type: application/json');
        try {
            if ($last_pagedata != $this->db->escape(json_encode($page_data))) {
                $this->db->query("insert into #__ams_revisions set ams_page_id = '" . (int) $id . "', "
                        . " user_id = '" . (int)$this->user->getId() . "', "
                        . " autosave = '" . (int) $autosave . "', "
                        . " pagedata = '" . $this->db->escape(json_encode($page_data)) . "', "
                        . " created = '" . time() . "', "
                        . " version = '" . $version . "'");
                $this->response->setOutput(json_encode(array('saved' => $this->db->insertId())));
            } else {
                $this->response->setOutput(json_encode(array('saved' => '1')));
            }
        } catch (Exception $e) {
            $this->response->setOutput(json_encode(array('saved' => '0')));
        }
    }

    public function autocomplete() {
        $json = array();


        if (isset($this->request->get['filter_name'])) {

            $query = $this->db->query("select * from #__ams_pages where name like '%" . $this->db->escape($this->request->get['filter_name']) . "%' and public='1' order by name asc limit 0,5");
            foreach ($query->rows as $row) {
                $json[] = array(
                    'ams_page_id' => $row['ams_page_id'],
                    'name' => strip_tags(html_entity_decode($row['name'], ENT_QUOTES, 'UTF-8'))
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
    
    
    public function get_revisions(){
        $this->language->load('cms/page');
        
        
        $ams_page_id = $this->request->get['ams_page_id'];
        $page = isset($this->request->get['page'])?$this->request->get['page']:1;
        $start = ($page - 1) * 20;

        $countq = $this->db->query("select count(*) as total from #__ams_revisions where ams_page_id = '" . (int)$ams_page_id . "'");
        $total = $countq->row['total'];
        $q = $this->db->query("select r.*, u.firstname, u.lastname from #__ams_revisions r left join #__user u on u.user_id = r.user_id where r.ams_page_id = '" . (int) $ams_page_id . "' order by r.ams_revision_id desc limit $start, 20");
        $this->data['histories'] = array();
        foreach($q->rows as $row){
            $row['author'] = $row['firstname'] . ' ' . $row['lastname'];
             $row['date_added'] = date("l dS F Y h:i:s A", $row['created']);
             $row['autosave'] = $row['autosave']? 'Yes':'No';
             if($this->request->get['ams_page_id']){
              $row['action'] = $this->url->link($this->_namespace . '/update', 'token=' . $this->session->data['token'] . '&ams_page_id=' . $this->request->get['ams_page_id'] . '&revision_id=' . $row['ams_revision_id'], 'SSL');
             }else{
                  $row['action'] = $this->url->link($this->_namespace . '/insert', 'token=' . $this->session->data['token'] . '&revision_id=' . $row['ams_revision_id'], 'SSL');
             }
              $this->data['histories'][] = $row;
        }
        
        
        $pagination = new \Core\Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url =  $this->url->link($this->_namespace . '/get_revisions', 'token=' . $this->session->data['token'] . '&ams_page_id=' . $this->request->get['ams_page_id'] . '&page={page}', 'SSL');
        $pagination->text = $this->language->get('text_pagination');

        $this->data['pagination'] = $pagination->render();
        
  
        
        $this->template = 'cms/partial/history.phtml';

        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    
    }

}
