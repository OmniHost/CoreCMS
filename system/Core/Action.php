<?php

namespace Core;

final class Action {

    /**
     * Filepath of the routed class
     * @var string 
     */
    protected $file;

    /**
     * Name of the routed class
     * @var string
     */
    protected $class;

    /**
     * name of the routed method
     * @var string
     */
    protected $method;

    /**
     * Arguments for the routed class
     * @var array
     */
    protected $args = array();

    /**
     * Action consttructor - Breaks the route into controller::action($args)
     * @param string $route
     * @param array $args
     */
    public function __construct($route, $args = array()) {
        $path = '';

        $parts = explode('/', str_replace('../', '', (string) $route));

        foreach ($parts as $part) {
            $path .= $part;

            if (is_dir(DIR_APPLICATION . 'controller/' . $path)) {
                $path .= '/';

                array_shift($parts);

                continue;
            }

            if (is_file(DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php')) {
                $this->file = DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';

                $this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

                array_shift($parts);

                break;
            }
        }


        if ($args) {
            $this->args = $args;
        }

        $method = array_shift($parts);

        if ($method) {
            $this->method = $method;
        } else {
            $this->method = 'index';
        }
    }

    /**
     * 
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * 
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * 
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * 
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    public function execute() {
        // Stop any magical methods being called
        if (substr($this->method, 0, 2) == '__') {
            return false;
        }

        if (is_file($this->file)) {
            include_once(__modification($this->file));

            $class = $this->class;

            $controller = new $class();

            if (is_callable(array($controller, $this->method))) {
               
                return call_user_func(array($controller, $this->method), $this->args);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
