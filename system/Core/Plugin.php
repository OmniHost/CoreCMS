<?php

namespace Core;

abstract class Plugin {

    protected $attributes = array();

    public function __construct() {
        $this->init();
    }

    abstract function init();

    /**
     * Returns object from registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    /**
     * Sets object to the registry
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    public function isAdmin() {
        return CORE_IS_ADMIN;
    }

    public function parseAttributes($default, $current) {
        $this->attributes = \Core\Shortcode::shortcodeAtts($default, $current);
        return $this->attributes;
    }

    public function attribute($key) {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : '';
    }

    public function addShortcode($tag, $func) {
        \Core\Shortcode::addShortcode($tag, $func);
    }

}
