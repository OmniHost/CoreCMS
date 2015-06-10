<?php

namespace Core;

abstract class Hook {

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

    public function addHook($tag, $callback, $priority = 10, $args = 1) {
        \Core\HookPoints::addHook($tag, $callback, $priority, $args);
    }
    
    public function isAdmin() {
        return CORE_IS_ADMIN;
    }

}
