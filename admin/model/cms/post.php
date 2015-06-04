<?php

class ModelBlogPost extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'blog.post';
    public $content;
    public $categories;
    public $tags;
    public $image;
}
