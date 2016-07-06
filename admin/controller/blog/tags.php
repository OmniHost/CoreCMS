<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Content - Blog Tags
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 * 
 */
 
class ControllerBlogTags extends \Core\Ams\Base {

    protected $_namespace = 'blog/tags';
    protected $_enableComments = false;
     protected $_enableParents = false;

     
     
     public function autoinsert(){
         $tag = $this->request->post['name'];
         $this->load->model('blog/tags');
         $tag_id = $this->model_blog_tags->findByName($tag);
         if(!$tag_id){
             $_pageModel = new \Core\Ams\Node();
              $tag_id = $_pageModel->createPage($tag, 'blog.tags', '0', '1', $this->config->get('config_layout_id'), '0');
         }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array('id' => $tag_id,'name' => $tag)));
         
     }

}