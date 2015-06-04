<?php

namespace Core;
class Document {

    /**
     * Document Title
     * @var string 
     */
    private $title;
    /**
     * Document Description
     * @var string 
     */
    private $description;
    /**
     * Document Keywords
     * @var string 
     */
    private $keywords;
    /**
     * List of links to add to document header
     * @var array
     */
    private $links = array();
    /**
     * List of styles to add to document header
     * @var array
     */
    private $styles = array();
    /**
     * List of scripts to add to document footer
     * @var array 
     */
    private $scripts = array();
    
    /**
     * list of custom meta tags for document header
     * @var array
     */
    private $meta = array();

    /**
     * Set the title for the current document
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Gets the current document title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function addLink($href, $rel) {
        $this->links[md5($href)] = array(
            'href' => $href,
            'rel' => $rel
        );
    }

    public function getLinks() {
        return $this->links;
    }

    public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
        $this->styles[md5($href)] = array(
            'href' => $href,
            'rel' => $rel,
            'media' => $media
        );
    }

    public function getStyles($combine = false) {
        if($combine){
            $styles = array();
            foreach($this->styles as $style){
                $styles[] = $style['href'];
            }
            return $styles;
        }
        return $this->styles;
    }
    
    public function resetStyles(){
        $this->styles = array();
    }

    public function addScript($script) {
        $this->scripts[md5($script)] = $script;
    }

    /**
     * 
     * @param string $key eg :og:name etc!!
     * @param string $meta
     */
    public function addMeta($key, $meta) {

        $this->meta[md5($key)] = $meta;
    }

    public function getMeta() {
        return $this->meta;
    }

    public function getScripts() {
      
        return $this->scripts;
    }
    
    public function resetScripts(){
        $this->scripts = array();
    }

}
