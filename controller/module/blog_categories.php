<?php

class ControllerModuleBlogCategories extends \Core\Controller {

    public function index() {
        $this->load->language('module/blog_categories');

        $data['heading_title'] = $this->language->get('heading_title');

        $group_id = $this->customer->getGroupId();
        // $cacheKey = 'blog_categories.' . $group_id;
        // $category_tree = $this->cache->get($cacheKey);
        //  if (!$category_tree) {
        $this->load->model('blog/category');
        $this->load->model('setting/rights');
        $categories = $this->model_blog_category->getPages(array('parent_id' => 0, 'status' => 1));

        $category_tree = array();
        foreach ($categories->rows as $category) {

            if ($this->model_setting_rights->getRight($category['ams_page_id'], 'ams_page')) {

                $category['children'] = array();
                if ($this->config->get('blog_categories_post_count')) {
                    $category['posts'] = $this->model_blog_category->countActivePosts($category);
                }
                $category['href'] = $this->url->link('blog/category', 'ams_page_id=' . $category['ams_page_id']);
                $children = $this->model_blog_category->getPages(array('parent_id' => $category['ams_page_id'], 'status' => 1));
                foreach ($children->rows as $child) {
                    if ($this->model_setting_rights->getRight($child['ams_page_id'], 'ams_page')) {
                        if ($this->config->get('blog_categories_post_count')) {
                            $child['posts'] = $this->model_blog_category->countActivePosts($child);
                        }
                         $child['href'] = $this->url->link('blog/category', 'ams_page_id=' . $child['ams_page_id']);
                        $category['children'][] = $child;
                    }
                }
                $category_tree[] = $category;
            }
        }

        //      $this->cache->set($cacheKey, $category_tree);
        //  }

        $data['blog_categories_post_count'] = $this->config->get('blog_categories_post_count');
        $data['category_tree'] = $category_tree;
        

        
        $this->data = $data;
        $this->template = 'module/blog_categories.phtml';
        return $this->render();
    }

}
