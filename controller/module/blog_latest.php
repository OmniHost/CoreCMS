<?php

class ControllerModuleBlogLatest extends \Core\Controller {

    public function index() {
        $this->load->language('module/blog_latest');

        
  
        $data['heading_title'] = html_entity_decode($this->config->get('blog_latest_title'));

        $group_id = $this->customer->getGroupId();
        // $cacheKey = 'blog_categories.' . $group_id;
        // $category_tree = $this->cache->get($cacheKey);
        //  if (!$category_tree) {
        $this->load->model('blog/category');
        $this->load->model('setting/rights');
        $posts = $this->model_blog_category->getLatestPosts(array('start' => 0, 'limit' => $this->config->get('blog_latest_post_count')));
        
        $data['posts'] = array();
        $this->load->model('tool/image');
        foreach($posts as $post){
                        $post = $this->model_blog_category->loadParent($post['ams_page_id'])->toArray();
            $post['image'] = $this->model_tool_image->resize($post['featured_image'],80,80);
            if(!$post['image']){
                $post['image'] = $this->model_tool_image->resize('no_image.jpg',80,80);
            }
            $post['name'] = html_entity_decode($post['name']);
            $post['date'] = DATE($this->language->get('post_date_format'), $post['publish_date']);
           
             $post['blurb'] = html_entity_decode($post['blurb']);
             $post['href'] = $this->url->link('blog/post', 'ams_page_id=' . $post['id']);
            $data['posts'][] = $post;
        }
        
        $this->data = $data;
        $this->template = 'module/blog_latest.phtml';
        return $this->render();
    }

}
