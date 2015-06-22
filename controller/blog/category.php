<?php

class ControllerBlogCategory extends \Core\Controller {

    public function index() {
        $this->load->model('blog/category');
        $page = $this->model_blog_category->loadPageObject($this->request->get['ams_page_id'])->toArray();

        if (!$page || !$page['status']) {
            return $this->not_found();
        }

        $this->load->model('setting/rights');
        $allowed = $this->model_setting_rights->getRight($this->request->get['ams_page_id'], 'ams_page');

        if ($allowed) {
            
            $pg = isset($this->request->get['page'])?$this->request->get['page']:1;

            $pg = isset($this->request->get['page'])?$this->request->get['page']:1;

            $this->model_blog_category->updateViews();
            $this->language->load('blog/category');

            $page['name'] = \Core\HookPoints::executeHooks('ams_page_name', $page['name']);


            $this->document->setTitle(strip_tags($page['meta_title']));
            $this->document->setKeywords($page['meta_keywords']);
            $this->document->setDescription($page['meta_description']);


            $this->document->addLink($this->url->link('blog/category', 'ams_page_id=' . $page['id']), 'canonical');

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $parent_id = $page['parent_id'];


            while ($parent_id > 0) {
                $parent_obj = $this->model_blog_category->loadParent($parent_id);

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
                'href' => $this->url->link('blog/category', 'ams_page_id=' . $page['id'])
            );

            $this->data['page'] = $page;



            $this->data['ams_page_id'] = $this->model_cms_page->id;


            $start = ($pg - 1) * $this->config->get('config_blog_limit');
            $start = ($pg - 1) * $this->config->get('config_blog_limit');
            

            $posts = $this->model_blog_category->getActivePosts(array('sort' => 'publish_date', 'order' => 'DESC','start' => $start, 'limit' => $this->config->get('config_blog_limit')));
            $total = $this->model_blog_category->countActivePosts();

            $this->data['posts'] = array();
            $this->load->model('blog/post');
            $this->load->model('tool/image');
            $this->load->model('cms/comment');
            foreach ($posts as $post) {
                $post = $this->model_blog_post->loadPageObject($post['ams_page_id'])->toArray();
                $post['featured_image'] = $this->model_tool_image->resizeCrop($post['featured_image'], $this->config->get('config_image_blogcat_width'), $this->config->get('config_image_blogcat_height'));
                $post['total_comments'] = $this->model_cms_comment->countComments($post['id']);
                $post['href'] = $this->url->link('blog/post', 'ams_page_id=' . $post['id']);
                $this->data['posts'][] = $post;
            }


            $pagination = new \Core\Pagination();
            $pagination->total = $total;
            $pagination->page = $pg;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->limit = $this->config->get('config_blog_limit');
            $pagination->url = $this->url->link('blog/category', 'ams_page_id=' . $page['id'] . '&page={page}', 'SSL');

            $this->data['pagination'] = $pagination;



            $this->template = 'blog/category.phtml';


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
