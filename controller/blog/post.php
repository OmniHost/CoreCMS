<?php

class ControllerBlogPost extends \Core\Controller {

    public function index() {
        $this->load->model('blog/post');
        $page = $this->model_blog_post->loadPageObject($this->request->get['ams_page_id'])->toArray();

        if (!$page || !$page['status'] || $page['publish_date'] > time()) {
            return $this->not_found();
        }

        $this->load->model('setting/rights');
        $allowed = $this->model_setting_rights->getRight($this->request->get['ams_page_id'], 'ams_page');

        if ($allowed) {

            $this->model_blog_post->updateViews();
            $this->language->load('blog/post');

            $page['comments'] = $this->load->controller('cms/page/commentblock', $page);
            $page['name'] = \Core\HookPoints::executeHooks('ams_page_name', $page['name']);
            $page['content'] = \Core\HookPoints::executeHooks('ams_page_content', html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8'));
            $page['href'] = $this->url->link('blog/post', 'ams_page_id=' . $page['id']);

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

            $this->document->addLink($this->url->link('blog/post', 'ams_page_id=' . $page['id']), 'canonical');
            $this->document->addMeta('og:url', $this->url->link('blog/post', 'ams_page_id=' . $page['id']), 'property');


            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $parent_id = $page['parent_id'];


            while ($parent_id > 0) {
                $parent_obj = $this->model_blog_post->loadParent($parent_id);

                $parent = $parent_obj->toArray();
                $parent_id = $parent['parent_id'];
                if ($parent['id']) {
                    $this->data['breadcrumbs'][] = array(
                        'text' => strip_tags($parent['name']),
                        'href' => $this->url->link(str_replace(".", "/", $parent_obj->getNamespace()), 'ams_page_id=' . $parent['id'])
                    );
                }
            }


            $this->data['breadcrumbs'][] = array(
                'text' => strip_tags($page['name']),
                'href' => $this->url->link('blog/post', 'ams_page_id=' . $page['id'])
            );

            $this->data['page'] = $page;



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

            $this->data['ams_page_id'] = $this->model_blog_post->id;

            $this->load->model('cms/comment');
            $this->language->load('cms/comment');
            $this->data['text_comments'] = $this->language->get('text_comments');
            $this->data['comment_count'] = $this->model_cms_comment->countComments($this->model_cms_page->id);



            $this->template = 'blog/post.phtml';


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

}
