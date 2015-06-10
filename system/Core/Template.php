<?php

namespace Core;

class Template {

    public $data = array();
    
    public $template;

    public function __construct() {
        
    }

    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    public function fetch($filename, $template = false) {


        if (!$template) {
            $template = DIR_TEMPLATE;
          }
if (NS != 'admin' && NS != 'installer') {
            $theme = $this->config->get('config_template');
            if ($theme) {
                if (is_file($template . $theme . '/' . $filename)) {
                    $template .= $theme . '/';
                } else {
                    $template .= 'default/';
                }
            } elseif (!is_file($template . $filename)) {
                $template .= 'default/';
            }
}
        
            $file = $template . $filename;


            if (file_exists($file)) {
                extract($this->data);
                ob_start();
                include($file);
                $content = ob_get_clean();
                return $content;
            } else {
                trigger_error('Error: Could not load template ' . $file . '!');
                exit();
            }
        }
}
