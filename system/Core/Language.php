<?php

namespace Core;

class Language {

    private $default = 'english';
    private $directory;
    private $data = array();

    public function __construct($directory) {
        $this->directory = $directory;
    }

    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : $key);
    }
    
    public function all(){
        return $this->data;
    }

    public function load($filename) {
        $file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();

            require(__modification($file));

            $this->data = array_deap_merge($this->data, $_);

            return $this->data;
        }

        $file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();

            require(__modification($file));

            $this->data = array_deap_merge($this->data, $_);

            return $this->data;
        }
    }

}
