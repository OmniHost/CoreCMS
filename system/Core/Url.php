<?php

namespace Core;

class Url {

    private $url;
    private $ssl;
    private $rewrite = array();

    public function __construct($url, $ssl = '') {
        $this->url = $url;
        $this->ssl = $ssl;
    }

    public function addRewrite($rewrite) {
        $this->rewrite[] = $rewrite;
    }

    public function rest($name, $args = '', $connection = 'NONSSL') {
       if ($connection == 'NONSSL') {
            $url = $this->url;
        } else {
            $url = $this->ssl;
        }
        
        $url .= 'rest/' . $name;
        
        if ($args) {
            if (is_array($args)) {
                $args = http_build_query($args);
            }
            $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
        }
        
        return $url;
    }
    
    /**
     * Rewrite the URL!
     * @param string $route
     * @param arry $args
     * @param string $connection NONSSL | SSL | CURRENT | PATH 
     * @return type
     */
    public function link($route, $args = '', $connection = 'NONSSL') {
        if ($connection == 'NONSSL') {
            $url = $this->url;
        } elseif ($connection == 'SSL') {
            $url = $this->ssl;
        } elseif ($connection == 'CURRENT') {
            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $url = $this->ssl;
            } else {
                $url = $this->url;
            }
            //PATH OPTION
        } else {
            $url = '';
        }

        $url .= 'index.php?p=' . $route;



        if ($args) {
            if (is_array($args)) {
                $args = http_build_query($args);
            }
            $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
        }

        foreach ($this->rewrite as $rewrite) {
            $url = $rewrite->rewrite($url);
        }

        return $url;
    }

}
