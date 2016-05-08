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

    public function path($name, $args = '') {
        
              
        $url = 'index.php?p=' . $route;

        if ($args) {
            if (is_array($args)) {
                $args = http_build_query($args);
            }
            $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
        }

        foreach ($this->rewrite as $rewrite) {
            $url = $rewrite->rewrite($url);
        }

        if ($connection == 'PATH') {
            $path = parse_url($url, PHP_URL_PATH);
            $query = parse_url($url, PHP_URL_QUERY);
            $frag = parse_url($url, PHP_URL_FRAGMENT);

            $url = $path;
            if ($query) {
                $url .= '?' . $query;
            }
            if ($frag) {
                $url .= '#' . $frag;
            }
        }
        return $url;
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
     * @param string $args
     * @param string $connection NONSSL | SSL | CURRENT | PATH 
     * @return type
     */
    public function link($route, $args = '', $connection = 'NONSSL') {
        if ($connection == 'NONSSL') {
            $url = $this->url;
        } elseif ($connection == 'SSL') {
            $url = $this->ssl;
        } else {
            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $url = $this->ssl;
            } else {
                $url = $this->url;
            }
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

        if ($connection == 'PATH') {
            $path = parse_url($url, PHP_URL_PATH);
            $query = parse_url($url, PHP_URL_QUERY);
            $frag = parse_url($url, PHP_URL_FRAGMENT);

            $url = $path;
            if ($query) {
                $url .= '?' . $query;
            }
            if ($frag) {
                $url .= '#' . $frag;
            }
        }
        return $url;
    }

}
