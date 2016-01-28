<?php

namespace Core;

class Config {

    /**
     * Holds the configuration data
     * @var array 
     */
    private $data = array();

    public function __construct($data = array()) {
        if ($data) {
            foreach ($data as $k => $v) {
                $this->set($k, $v);
            }
        }
    }

    /**
     * return the configuration variable if set
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    /**
     * Sets configuration variable
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * checks if a configuration key exists
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * load an additional configuration file from the config directory (3rd party intergrations)
     * @param string $filename
     */
    public function load($filename, $path = false) {
        if (!$path) {
            $path = DIR_CONFIG;
        }
        $file = $path . $filename . '.php';
      
        if (file_exists($file)) {
            $_ = array();

            require( __modification($file));

            $this->data = array_deap_merge($this->data, $_);
        } else {
            throw new \Core\Exception('Error: Could not load config ' . $file . '!');
            exit();
        }
    }
    
    public function keys(){
        return array_keys($this->data);
    }

}
