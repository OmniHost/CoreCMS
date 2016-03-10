<?php

class ControllerBlogBlog extends \Core\Controller {

    public function index() {
        $this->load->model('blog/category');
        $this->language->load('blog/category');

        $page['name'] = $this->language->get('heading_archives');

        $this->data['heading_title'] = $page['name'];

        $this->document->setTitle(strip_tags( $page['name']));


        $this->document->addLink($this->url->link('blog/blog'), 'canonical');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

       
        $this->data['breadcrumbs'][] = array(
            'text' => strip_tags($page['name']),
            'href' => $this->url->link('blog/blog')
        );

        $this->data['page'] = $page;



        $this->data['ams_page_id'] = '0';

        $pg = isset($this->request->get['page'])?$this->request->get['page']:1;
         $start = ($pg - 1) * $this->config->get('config_blog_limit');

        $posts = $this->model_blog_category->getLatestPosts(array('sort' => 'publish_date', 'order' => 'DESC', 'start' => $start, 'limit' => $this->config->get('config_blog_limit')));
        $total = $this->model_blog_category->countLatestPosts();

   
        
        
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
            $pagination->url = $this->url->link('blog/blog', 'page={page}', 'SSL');

            $this->data['pagination'] = $pagination;


        $this->template = 'blog/blog.phtml';


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
    }

}
