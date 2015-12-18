<?php

class ControllerFeedRssFeed extends \Core\Controller {

    public function index() {


        if ($this->config->get('rss_feed_status')) {


            $rss = $this->cache->get('rss_feed');
            if (!$rss) {
                $rss = array(
                    'last_build' => DATE("r"),
                    'items' => array()
                );

                $this->load->model('blog/category');
                $this->load->model('blog/post');
                $this->load->model('tool/image');
                $this->load->model('cms/comment');
                $posts = $this->model_blog_category->getLatestPosts(array('sort' => 'publish_date', 'order' => 'DESC', 'start' => 0, 'limit' => 15));
                foreach ($posts as $post) {
                    $post = $this->model_blog_post->loadPageObject($post['ams_page_id'])->toArray();

                    $post['featured_image'] = $this->model_tool_image->resizeCrop($post['featured_image'], $this->config->get('config_image_blogcat_width'), $this->config->get('config_image_blogcat_height'));
                    $post['total_comments'] = $this->model_cms_comment->countComments($post['id']);
                    $post['href'] = $this->url->link('blog/post', 'ams_page_id=' . $post['id']);
                    $post['author'] = $this->db->query("select CONCAT(firstname,' ', lastname) as author from #__user where user_id='" . (int) $post['user_id'] . "'")->row['author'];

                    $rss['items'][] = $post;
                }
                
                $this->cache->set('rss_feed', $rss, 60*60);
            }

            $xml = '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">';
            $xml .= "<channel>";
            $xml .= '<title><![CDATA[' . $this->config->get('config_name') . ']]></title>
 
<atom:link href="' . $this->url->link('feed/rss_feed') . '" rel="self" type="application/rss+xml"/>
<link>' . $this->config->get('config_url') . '</link>
 
<description>' . $this->config->get('config_meta_description') . '</description>
 
<lastBuildDate>' . $rss['last_build'] . '</lastBuildDate>
 
<language>' . $this->language->get('code') . '</language>
 
<sy:updatePeriod>hourly</sy:updatePeriod>
 
<sy:updateFrequency>1</sy:updateFrequency>
 
<generator>https://github.com/OmniHost/CoreCMS</generator>';

            foreach ($rss['items'] as $item) {
                $xml .= '<item>
<title><![CDATA[' . $item['name'] . ']]></title>
<link>' . $item['href'] . '</link>
 
<comments>' . $item['href'] . '#commentsarea</comments>
 
<pubDate>' . DATE("r", $item['publish_date']) . '</pubDate>
 
<dc:creator><![CDATA[' . $item['author'] . ']]></dc:creator>';

                foreach ($item['categories'] as $category) {
                    $xml .= '<category><![CDATA[' . $category['name'] . ']]></category>';
                }

                $xml .= '<description><![CDATA[' . $item['blurb'] . ']]></description>

<content:encoded><![CDATA[ ' . $item['content'] . ']]></content:encoded>


<slash:comments>' . (int) $item['total_comments'] . '</slash:comments>
</item>';
            }

            $xml .= '</channel>
</rss>';


            $this->response->addHeader('Content-Type: application/xml');
            $this->response->setOutput($xml);
        }
    }

}
