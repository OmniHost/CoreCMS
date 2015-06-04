<?php


namespace Core;

final class Registry {

    /**
     * holds the registry items
     * @var array 
     */
    private $data = array();

    /**
     * Holds the current \Core\Registry Instance
     * @var \Core\Registry 
     */
    private static $_instance;

    /**
     * constructor
     */
    public function __construct() {
        self::$_instance = $this;
    }

    /**
     * 
     * @return \Core\Registry 
     */
    public static function getInstance() {
        return self::$_instance;
    }

    /**
     * Returns item from registry or null if not set
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    /**
     * Adds an item to the registyr
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Does the item exist in the registyr
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * Magic method for lazy people to use to shortcut get('key') to ->key
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

}
