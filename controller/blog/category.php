<?php

class ControllerBlogCategory extends \Core\Controller\Page {

    protected $_namespace = 'blog/category';

    public function index() {

        $this->getPage();
        if ($this->_error) {
            return $this->showErrorPage();
        }

        $pg = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $this->data['ams_page_id'] = $this->model_cms_page->id;


        $start = ($pg - 1) * $this->config->get('config_blog_limit');

        $posts = $this->_model->getActivePosts(array('sort' => 'publish_date', 'order' => 'DESC', 'start' => $start, 'limit' => $this->config->get('config_blog_limit')));
        $total = $this->_model->countActivePosts();

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

        $this->response->setOutput($this->render());
    }

}
