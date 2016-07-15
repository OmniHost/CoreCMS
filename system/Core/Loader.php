<?php

namespace Core;

final class Loader {

    public function __construct() {
        
    }

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

    /**
     * Loads Library class
     * @todo - add to allow additional libraries
     * @todo is this really required anymore ?AUTOLOADER?
     * @param string $library
     */
    public function library($library, $path = false) {

        if (!$path) {
            $path = DIR_APPLICATION;
        }
        $file = $path . 'library/' . $library . '.php';


        if (file_exists($file)) {
            include_once(__modification($file));
        } else {
            throw new \Core\Exception('Error: Could not load library ' . $library . '!');
            exit();
        }
    }

    /**
     * Loads helper class
     * @param string $helper
     */
    public function helper($helper) {
        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {
            include_once(__modification($file));
        } else {
            throw new \Core\Exception('Error: Could not load helper ' . $helper . '!');
            exit();
        }
    }

    public function controller($route, $args = array()) {
        $action = new \Core\Action($route, $args);
        return $action->execute();
    }

    /**
     * Loads model for the current application namespace
     * @param string $model
     */
    public function model($model, $path = false) {

        if (!$path) {
            $path = DIR_APPLICATION;
        }

        $file = $path . 'model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once(__modification($file));
            $regclass = new $class($this->registry);
            \Core\Core::$registry->set('model_' . str_replace('/', '_', $model), $regclass);
            return $regclass;
        } else {
            throw new \Core\Exception('Error: Could not load model ' . $model . '!');
            exit();
        }
    }

    /**
     * @deprecated since version number
     * initialize the database
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function database($driver, $hostname, $username, $password, $database) {
        $file = DIR_SYSTEM . 'database/' . $driver . '.php';
        $class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);

        if (file_exists($file)) {
            include_once(__modification($file));

            \Core\Core::$registry->set(str_replace('/', '_', $driver), new $class($hostname, $username, $password, $database));
        } else {
            throw new \Core\Exception('Error: Could not load database ' . $driver . '!');
            exit();
        }
    }

    /**
     * Load Configuration Helper
     * @param string $config path to configuration file
     */
    public function config($config) {
        $this->config->load($config);
    }

    /**
     * Loads a file into the langauge
     * @param string $language
     * @return array Language translation array
     */
    public function language($language) {
        return $this->language->load($language);
    }

    public function view($route, $data = array()) {
        // Sanitize the call
        $route = str_replace('../', '', (string) $route);
        if (substr($route, -6) == '.phtml') {

            $route = substr($route, 0, -6);
        }

        // Trigger the pre events
        $this->event->trigger('view/' . $route . '/before', $data);


        $template = new \Core\Template('basic');

        foreach ($data as $key => $value) {
            $template->set($key, $value);
        }

        $output = $template->fetch($route . '.phtml');

        // Trigger the post e
        $this->event->trigger('view/' . $route . '/after', $output);

        return $output;
    }

}
