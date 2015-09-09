<?php

class ControllerCmsPage extends \Core\Controller {

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
                'link' => $this->url->link('account/account','','SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Login Page',
                'type' => 'core',
                'link' => $this->url->link('account/login','','SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Register Page',
                'type' => 'core',
                'link' => $this->url->link('account/register','','SSL'),
                'ams_page_id' => "0"
            ),
            array(
                'name' => 'Logout Page',
                'type' => 'core',
                'link' => $this->url->link('account/logout','','SSL'),
                'ams_page_id' => "0"
            )
        );
        
         $pages = \Core\HookPoints::executeHooks('get_core_pages', $core_pages);
         
         usort($pages, function($a, $b) { 
             return strcmp($a['name'], $b['name']);
         });
         return $pages;
    }

    public function autocomplete() {
        $pages = $this->getCorePages();

        $search = $this->request->get['filter_page'];

        
        
        /*if (strpos('home page', strtolower($search)) !== false || strlen($search) < 3) {
            $pages[] = array(
                'name' => 'Home Page',
                'type' => 'core',
                'link' => $this->url->link('common/home'),
                'ams_page_id' => "0"
            );
        }
        if (strpos('contact page', strtolower($search)) !== false || strlen($search) < 3) {
            $pages[] = array(
                'name' => 'Contact Page',
                'type' => 'core',
                'link' => $this->url->link('common/contact'),
                'ams_page_id' => "0"
            );
        }*/
        if (strlen($search) >= 3) {
            
            foreach($pages as $idx=>$page){
                if( strpos( strtolower($page['name']), strtolower($search)) === false){
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
        $pages = \Core\HookPoints::executeHooks('page_links_list', $pages, array('filter_name' => $search));

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

        $pages = \Core\HookPoints::executeHooks('page_links_list', $pages);

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

    public function index() {
        $this->load->model('cms/page');
        $page = $this->model_cms_page->loadPageObject($this->request->get['ams_page_id'])->toArray();

        if (!$page || !$page['status']) {
            return $this->not_found();
        }

        $this->load->model('setting/rights');
        $allowed = $this->model_setting_rights->getRight($this->request->get['ams_page_id'], 'ams_page');

        if ($allowed) {

            
            
            $this->model_cms_page->updateViews();
            $this->language->load('cms/page');

            $page['comments'] = $this->load->controller('cms/page/commentblock', $page);
            $page['name'] = \Core\HookPoints::executeHooks('ams_page_name', $page['name']);
            $page['content'] = \Core\HookPoints::executeHooks('ams_page_content', html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8'));

 
      

            $this->document->setTitle(strip_tags($page['meta_title']));
            $this->document->setKeywords($page['meta_keywords']);
            $this->document->setDescription($page['meta_description']);
            if(!empty($page['meta_og_title'])){
                $this->document->addMeta('og:title', $page['meta_og_title'],'property');
            }
            if(!empty($page['meta_og_description'])){
                $this->document->addMeta('og:description', $page['meta_og_description'],'property');
            }
            if(!empty($page['meta_og_image'])){
                $this->document->addMeta('og:image', $this->config->get('config_ssl') .'img/' .  $page['meta_og_image'],'property');
            }

            $this->document->addLink($this->url->link('cms/page', 'ams_page_id=' . $page['id']), 'canonical');
            $this->document->addMeta('og:url', $this->url->link('cms/page', 'ams_page_id=' . $page['id']),'property');

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $parent_id = $page['parent_id'];


            $parentCrumbs = array();
            
            while ($parent_id > 0) {
                $parent_obj = $this->model_cms_page->loadParent($parent_id);

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
            foreach($parentCrumbs as $crumb){
                 $this->data['breadcrumbs'][] = $crumb;
            }

            

            $this->data['breadcrumbs'][] = array(
                'text' => strip_tags($page['name']),
                'href' => $this->url->link('cms/page', 'ams_page_id=' . $page['id'])
            );

            $this->data['page'] = $page;

            


            if ($page['comments'] && $this->config->get('config_review_status')) {
                $this->data['has_comments'] = true;
            } else {
                $this->data['has_comments'] = false;
            }

            
           

            $this->template = 'cms/page.phtml';


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

            $this->response->setOutput($this->render());
        } else {
            return $this->forward('error/not_allowed');
        }
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

            $page['name'] = \Core\HookPoints::executeHooks('ams_page_name', $page['name']);
            $page['content'] = \Core\HookPoints::executeHooks('ams_page_content', html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8'));

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
    
    
    public function commentblock($page){
        
        if(!$page['comments']){
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

}
