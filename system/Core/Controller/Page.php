<?php

namespace Core\Controller;

abstract class Page extends \Core\Controller {

    protected $_model;
    protected $_error = false;

    protected function getPage() {
        $this->_model = $this->load->model($this->_namespace);

        $page = $this->_model->loadPageObject($this->request->get['ams_page_id'])->toArray();




        if (!$page) {
            $this->_error = 'not_found';
        } elseif (!$page['status']) {
            $this->user = new \Core\User();
            if (!$this->user->isLogged()) {
                $this->_error = 'not_found';
            }
        }

        if (!$this->_error) {
            $this->load->model('setting/rights');
            $allowed = $this->model_setting_rights->getRight($this->request->get['ams_page_id'], 'ams_page');
            if (!$allowed) {
                $this->_error = 'not_allowed';
            }
        }

        if (!$this->_error) {
            $this->_model->updateViews();
            $this->language->load('cms/page');
            if ($this->_namespace != 'cms/page') {
                $this->language->load($this->_namespace);
            }

            $page['comments'] = $this->load->controller($this->_namespace . '/commentblock', $page);
            $this->event->trigger('ams.page.name', $page['name']);
            $page['slug'] = $this->_model->getSlug();




            $this->document->setTitle(strip_tags($page['meta_title']));
            $this->document->setKeywords($page['meta_keywords']);
            $this->document->setDescription($page['meta_description']);



            if (!empty($page['meta_og_title'])) {
                $this->document->addMeta('og:title', $page['meta_og_title'], 'property');
            }
            if (!empty($page['meta_og_description'])) {
                $this->document->addMeta('og:description', $page['meta_og_description'], 'property');
            }
            if (!empty($page['meta_og_image'])) {
                $this->document->addMeta('og:image', $this->config->get('config_ssl') . 'img/' . $page['meta_og_image'], 'property');
            }

            $this->document->addLink($this->url->link($this->_namespace, 'ams_page_id=' . $page['id']), 'canonical');
            $this->document->addMeta('og:url', $this->url->link($this->_namespace, 'ams_page_id=' . $page['id']), 'property');

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $parent_id = $page['parent_id'];


            $parentCrumbs = array();

            while ($parent_id > 0) {
                $parent_obj = $this->_model->loadParent($parent_id);

                $parent = $parent_obj->toArray();
                $parent_id = $parent['parent_id'];
                if ($parent['id']) {
                    $parentCrumbs[] = array(
                        'text' => strip_tags($parent['name']),
                        'href' => $this->url->link(str_replace(".", "/", $parent_obj->getNamespace()), 'ams_page_id=' . $parent['id'])
                    );
                }
            }

            $parentCrumbs = array_reverse($parentCrumbs);
            foreach ($parentCrumbs as $crumb) {
                $this->data['breadcrumbs'][] = $crumb;
            }



            $this->data['breadcrumbs'][] = array(
                'text' => strip_tags($page['name']),
                'href' => $this->url->link($this->_namespace, 'ams_page_id=' . $page['id'])
            );

            $this->data['page'] = $page;

            if ($page['comments'] && $this->config->get('config_review_status')) {
                $this->data['has_comments'] = true;
            } else {
                $this->data['has_comments'] = false;
            }
            
             $this->load->model('cms/comment');
        $this->data['comment_count'] = $this->model_cms_comment->countComments($page['id']);
            

            $this->template = $this->_namespace . '.phtml';

            $theme = $this->config->get('config_template');
            $override = $this->_namespace . '/' . $this->_model->getSlug() . '.phtml';

            if (is_file(DIR_TEMPLATE . $theme . '/' . $override)) {
                $this->template = $override;
            }



            $this->children = array(
                'common/column_top',
                'common/column_bottom',
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );
        }
    }

    protected function showErrorPage() {
        call_user_func(array($this, $this->_error));
    }

    protected function getCorePages() {

        $core_pages = array(
            array(
                'name' => 'Home Page',
                'type' => 'core',
                'link' => $this->url->link('common/home'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Contact Page',
                'type' => 'core',
                'link' => $this->url->link('common/contact'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Blog Page',
                'type' => 'core',
                'link' => $this->url->link('blog/blog'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Account Page',
                'type' => 'core',
                'link' => $this->url->link('account/account', '', 'SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Login Page',
                'type' => 'core',
                'link' => $this->url->link('account/login', '', 'SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Register Page',
                'type' => 'core',
                'link' => $this->url->link('account/register', '', 'SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Logout Page',
                'type' => 'core',
                'link' => $this->url->link('account/logout', '', 'SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'FAQ Page',
                'type' => 'core',
                'link' => $this->url->link('module/faq', '', 'SSL'),
                'ams_page_id' => "0",
                'config' => 'faq_status'
            )
        );

        $this->event->trigger('cms.pagelist', $core_pages);



        usort($core_pages, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $core_pages;
    }

    public function autocomplete() {
        $pages = $this->getCorePages();

        $search = $this->request->get['filter_page'];

        if (strlen($search) >= 3) {

            foreach ($pages as $idx => $page) {
                if (strpos(strtolower($page['name']), strtolower($search)) === false) {
                    unset($pages[$idx]);
                }
                if (!empty($page['config']) && !$this->config->get($page['config'])) {
                    unset($pages[$idx]);
                }
            }

            $query = $this->db->query("select * from #__ams_pages where public='1'and name like '%" . $this->db->escape($search) . "%' order by name asc limit 10");
            foreach ($query->rows as $row) {
                $pages[] = array(
                    'name' => $row['name'],
                    'type' => 'ams_page',
                    'link' => $this->url->link(str_replace(".", "/", $row['namespace']), 'ams_page_id=' . $row['ams_page_id']),
                    'ams_page_id' => $row['ams_page_id']
                );
            }
        }

        $this->event->trigger('pages.autocomplete.list', $pages);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($pages));
    }

    public function ajaxlist() {
        $html = "CKEDITOR.plugins.add('internlink');";
//$pages .= "var InternPagesSelectBox = [ ['Basketball','http://goto.baseball' ], [ 'Baseball' ], [ 'Hockey' ], [ 'Football' ] ];";
        $html.= "var InternPagesSelectBox = ";
        $this->response->addHeader('Content-type: application/javascript');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
        $this->response->addHeader('Cache-Control: post-check=0, pre-check=0, false');
        $this->response->addHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        $this->response->addHeader('Pragma: no-cache');

        $pages = $this->getCorePages();
        $query = $this->db->query("select * from #__ams_pages where public='1' order by name asc");
        foreach ($query->rows as $row) {
            $pages[] = array(
                'name' => $row['name'],
                'type' => 'ams_page',
                'link' => $this->url->link(str_replace(".", "/", $row['namespace']), 'ams_page_id=' . $row['ams_page_id']),
                'ams_page_id' => $row['ams_page_id']
            );
        }

        $this->event->trigger('pages.autocomplete.list', $pages);

        $json = array();
        foreach ($pages as $page) {
            $json[] = array(
                $page['name'], $page['link']
            );
        }


        $html .= json_encode($json) . ";\n";

        $html .= "CKEDITOR.on('dialogDefinition', function (event) {"
                . "var dialogName = event.data.name;"
                . "var dialogDefinition = event.data.definition;"
                . "if ( dialogName == 'link' ) {"
                . "var infoTab = dialogDefinition.getContents('info');"
                . "infoTab.add( {
         type : 'select',
         id : 'intern',
         label : 'Intern Page',
         'default' : '',
         style : 'width:100%',
         items : InternPagesSelectBox,
         onChange : function()
            {
                var d = CKEDITOR.dialog.getCurrent();
                d.setValueOf('info', 'url', this.getValue());
                if (!this.getValue()){ d.setValueOf('info', 'protocol','http://')};
               
            },
         setup : function( data )
         {
            this.allowOnChange = false;
            this.setValue( data.url ? data.url.url : '' );
            this.allowOnChange = true;
         }
        }, 'browse' );
        
        dialogDefinition.onLoad = function()
        {
            var internField = this.getContentElement( 'info', 'intern' );
            internField.reset();
        };
   }";

        $html .= "});";

        $this->response->setOutput($html);
    }

    public function agree() {
        $this->load->model('cms/page');
        $page = $this->model_cms_page->loadPageObject($this->request->get['page_id'])->toArray();

        if (!$page || !$page['status']) {
            return $this->not_found();
        }

        $this->load->model('setting/rights');
        $allowed = $this->model_setting_rights->getRight($this->request->get['page_id'], 'ams_page');

        if ($allowed) {

            $this->model_cms_page->updateViews();
            $this->language->load('cms/page');

            $this->event->trigger('ams.page.name', $page['name']);
            $page['content'] = html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8');
            $this->event->trigger('ams.page.content', $page['content']);
            $this->data['page'] = $page;

            $this->template = 'cms/agree.phtml';

            $this->response->setOutput($this->render());
        } else {
            return $this->forward('error/not_allowed');
        }
    }

    public function comment() {
        $this->load->model('setting/rights');

        if (isset($this->request->get['page_id']) && $this->model_setting_rights->getRight($this->request->get['page_id'], 'ams_page')) {

            $this->load->language('cms/comment');

            $this->load->model('cms/comment');

            $data['text_no_reviews'] = $this->language->get('text_no_reviews');
            $data['button_place_comment'] = $this->language->get('button_place_comment');

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            $data['comments'] = array();


            $comment_total = $this->model_cms_comment->countComments($this->request->get['page_id']);

            $results = $this->model_cms_comment->getComments($this->request->get['page_id'], ($page - 1) * 5, 5);

            foreach ($results as $result) {
                $data['comments'][] = array(
                    'author' => $result['author'],
                    'text' => nl2br($result['text']),
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
                );
            }
            $pagination = new \Core\Pagination();
            $pagination->total = $comment_total;
            $pagination->page = $page;
            $pagination->limit = 5;
            $pagination->url = $this->url->link('cms/page/comment', 'page_id=' . $this->request->get['page_id'] . '&page={page}');
            $this->text = $this->language->get('text_pagination');


            $data['pagination'] = $pagination->render();

            $this->data = $data;
            $this->template = 'cms/comments.phtml';
            $this->response->setOutput($this->render());
        }
    }

    public function write() {

        $this->load->model('setting/rights');

        if (isset($this->request->get['page_id']) && $this->model_setting_rights->getRight($this->request->get['page_id'], 'ams_page')) {

            $this->load->language('cms/comment');

            $json = array();

            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
                    $json['error']['name'] = $this->language->get('error_name');
                }

                if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                    $json['error']['text'] = $this->language->get('error_text');
                }

                if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
                    $json['error']['captcha'] = $this->language->get('error_captcha');
                }

                unset($this->session->data['captcha']);

                if (!isset($json['error'])) {
                    $this->load->model('cms/comment');

                    $this->model_cms_comment->addComment($this->request->get['page_id'], $this->request->post);

                    if ($this->config->get('config_comment_auto_approve')) {
                        $json['success'] = $this->language->get('text_success_approved');
                    } else {
                        $json['success'] = $this->language->get('text_success');
                    }
                }
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

    public function commentblock($page) {

        if (!$page['comments']) {
            return '';
        }

        if ($page['comments'] && $this->config->get('config_review_status')) {
            $this->data['has_comments'] = true;
        } else {
            $this->data['has_comments'] = false;
        }

        if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
            $this->data['can_comment'] = true;
        } else {
            $this->data['can_comment'] = false;
        }

        $this->data['comment_auto_approve'] = $this->config->get('config_comment_auto_approve');


        if ($this->customer->isLogged()) {
            $this->data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
        } else {
            $this->data['customer_name'] = '';
        }

        $this->data['ams_page_id'] = $page['id'];

        $this->load->model('cms/comment');
        $this->language->load('cms/comment');
        $this->data['text_comments'] = $this->language->get('text_comments');
        $this->data['comment_count'] = $this->model_cms_comment->countComments($page['id']);


        $this->template = 'cms/commentblock.phtml';
        return $this->render();
    }

    public function download() {



        $this->load->model('setting/rights');

        if (isset($this->request->get['download_id']) && isset($this->request->get['ams_page_id']) && $this->model_setting_rights->getRight($this->request->get['ams_page_id'], 'ams_page')) {
            $download_id = $this->request->get['download_id'];
        } else {
            $download_id = 0;
        }

        $this->load->model('cms/download');

        $download_info = $this->model_cms_download->getDownload($download_id);

        if ($download_info) {
            $file = DIR_DOWNLOAD . $download_info['filename'];
            $mask = basename($download_info['mask']);

            if (!headers_sent()) {
                if (file_exists($file)) {

                    $this->model_cms_download->updateDownloaded($download_id);

                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));

                    if (ob_get_level()) {
                        ob_end_clean();
                    }

                    readfile($file, 'rb');

                    exit();
                } else {
                    exit('Error: Could not find file ' . $file . '!');
                }
            } else {
                exit('Error: Headers already sent out!');
            }
        } else {
            $this->response->redirect($this->url->link('error/not_found', '', 'SSL'));
        }
    }

}
