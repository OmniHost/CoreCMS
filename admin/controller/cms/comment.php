<?php

class ControllerCmsComment extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('cms/comment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/comment');

        $this->getList();
    }

    public function add() {
        $this->load->language('cms/comment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/comment');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_cms_comment->addComment($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_page'])) {
                $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('cms/comment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/comment');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_cms_comment->editComment($this->request->get['comment_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_page'])) {
                $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('cms/comment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('cms/comment');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $comment_id) {
                $this->model_cms_comment->deleteComment($comment_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_page'])) {
                $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['filter_page'])) {
            $filter_page = $this->request->get['filter_page'];
        } else {
            $filter_page = null;
        }

        if (isset($this->request->get['filter_author'])) {
            $filter_author = $this->request->get['filter_author'];
        } else {
            $filter_author = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'r.date_added';
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

        $url = '';

        if (isset($this->request->get['filter_page'])) {
            $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

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
            'href' => $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('cms/comment/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('cms/comment/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['comments'] = array();

        $filter_data = array(
            'filter_page' => $filter_page,
            'filter_author' => $filter_author,
            'filter_status' => $filter_status,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $comment_total = $this->model_cms_comment->getTotalComments($filter_data);

        $results = $this->model_cms_comment->getComments($filter_data);

        foreach ($results as $result) {
            $data['comments'][] = array(
                'comment_id' => $result['comment_id'],
                'name' => html_entity_decode($result['name']),
                'author' => $result['author'],
                'rating' => $result['rating'],
                'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit' => $this->url->link('cms/comment/edit', 'token=' . $this->session->data['token'] . '&comment_id=' . $result['comment_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['column_page'] = $this->language->get('column_page');
        $data['column_author'] = $this->language->get('column_author');
        $data['column_rating'] = $this->language->get('column_rating');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_page'] = $this->language->get('entry_page');
        $data['entry_author'] = $this->language->get('entry_author');
        $data['entry_rating'] = $this->language->get('entry_rating');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

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

        $data['sort_page'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&sort=id.name' . $url, 'SSL');
        $data['sort_author'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&sort=r.author' . $url, 'SSL');
        $data['sort_rating'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&sort=r.rating' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&sort=r.status' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . '&sort=r.date_added' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_page'])) {
            $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $comment_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
        $pagination->text = $this->language->get('text_pagination');

        $data['pagination'] = $pagination->render();


        $data['filter_page'] = $filter_page;
        $data['filter_author'] = $filter_author;
        $data['filter_status'] = $filter_status;
        $data['filter_date_added'] = $filter_date_added;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');
        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        $this->data = $data;
        $this->template = 'cms/comment_list.phtml';

        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['comment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_page'] = $this->language->get('entry_page');
        $data['entry_author'] = $this->language->get('entry_author');
        $data['entry_rating'] = $this->language->get('entry_rating');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_text'] = $this->language->get('entry_text');

        $data['help_page'] = $this->language->get('help_page');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['page'])) {
            $data['error_page'] = $this->error['page'];
        } else {
            $data['error_page'] = '';
        }

        if (isset($this->error['author'])) {
            $data['error_author'] = $this->error['author'];
        } else {
            $data['error_author'] = '';
        }

        if (isset($this->error['text'])) {
            $data['error_text'] = $this->error['text'];
        } else {
            $data['error_text'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_page'])) {
            $url .= '&filter_page=' . urlencode(html_entity_decode($this->request->get['filter_page'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

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
            'href' => $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['comment_id'])) {
            $data['action'] = $this->url->link('cms/comment/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('cms/comment/edit', 'token=' . $this->session->data['token'] . '&comment_id=' . $this->request->get['comment_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('cms/comment', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['comment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $comment_info = $this->model_cms_comment->getComment($this->request->get['comment_id']);
        }
        
   
        $data['token'] = $this->session->data['token'];

        // $this->load->model('catalog/information');

        if (isset($this->request->post['ams_page_id'])) {
            $data['ams_page_id'] = $this->request->post['ams_page_id'];
        } elseif (!empty($comment_info)) {
            $data['ams_page_id'] = $comment_info['ams_page_id'];
        } else {
            $data['ams_page_id'] = '';
        }

        if (isset($this->request->post['ams_page'])) {
            $data['ams_page'] = $this->request->post['information'];
        } elseif (!empty($comment_info)) {
            $data['ams_page'] = $comment_info['page'];
        } else {
            $data['ams_page'] = '';
        }

        if (isset($this->request->post['author'])) {
            $data['author'] = $this->request->post['author'];
        } elseif (!empty($comment_info)) {
            $data['author'] = $comment_info['author'];
        } else {
            $data['author'] = '';
        }

        if (isset($this->request->post['text'])) {
            $data['text'] = $this->request->post['text'];
        } elseif (!empty($comment_info)) {
            $data['text'] = $comment_info['text'];
        } else {
            $data['text'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($comment_info)) {
            $data['status'] = $comment_info['status'];
        } else {
            $data['status'] = '';
        }
        
        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        $this->data = $data;
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->template = 'cms/comment_form.phtml';

        $this->response->setOutput($this->render());
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'cms/comment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
      //  debugPre($this->request->post);
      //  exit;

        if (!$this->request->post['ams_page_id']) {
            $this->error['page'] = $this->language->get('error_page');
        }

        if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
            $this->error['author'] = $this->language->get('error_author');
        }

        if (utf8_strlen($this->request->post['text']) < 1) {
            $this->error['text'] = $this->language->get('error_text');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'cms/comment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_page'])) {

            $this->load->model('cms/comment');

            $filter_data = array(
                'filter_page' => $this->request->get['filter_page'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_cms_comment->getComments($filter_data);

            foreach ($results as $result) {

                $json[] = array(
                    'comment_id' => $result['comment_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        if (isset($this->request->get['filter_author'])) {

            $this->load->model('cms/comment');

            $filter_data = array(
                'filter_author' => $this->request->get['filter_author'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_cms_comment->getComments($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'comment_id' => $result['comment_id'],
                    'name' => $result['author']
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
