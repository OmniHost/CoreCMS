<?php

namespace Core;

Class Core {

    public $pre_actions = array();
    public $default_route = 'common/home';
    public $error_route = 'error/not_found';

    /**
     * @var \Core\Registry
     */
    public static $registry;

    public function __construct($config_data = array(), $ns = '') {

        self::registerAutoloader();


        self::$registry = new \Core\Registry();

        $config = new \Core\Config($config_data);
        self::$registry->set('config', $config);

        if (is_file(DIR_SYSTEM . 'config/config.php')) {
            $config->load('config', DIR_SYSTEM . 'config/');
        }



        $hostname = str_replace('www.', '', $_SERVER['HTTP_HOST']);


        if (is_file(DIR_CONFIG . $hostname . '.php')) {
            $config->load($hostname);
        }
        if (is_file(DIR_CONFIG . $hostname . '.' . $ns . '.php')) {
            $config->load($hostname . '.' . $ns);
        }

        require_once('Event.php');
        $event = new \Core\Event();
        self::$registry->set('event', $event);


        define('CORE_IS_ADMIN', $ns == 'admin');
        if ($ns != 'installer') {
            require_once('Db.php');

            $db = new \Core\Db($config->get('DB_DRIVER'), $config->get('DB_HOSTNAME'), $config->get('DB_USERNAME'), $config->get('DB_PASSWORD'), $config->get('DB_DATABASE'), $config->get('DB_PREFIX'));

            if ($config->get('DB_LOG')) {
                $db->profile = true;
                register_shutdown_function(array($db, 'profile'));
            }
            self::$registry->set('db', $db);

            $dbconfigs = $db->fetchAll("select * from #__setting");
            foreach ($dbconfigs as $setting) {
                if (!$setting['serialized']) {
                    $config->set($setting['key'], $setting['value']);
                } else {
                    $config->set($setting['key'], unserialize($setting['value']));
                }
            }

            //Core Events:::
            $this->event->register('cms.pagelist', 'cms/page/event_pagelist');
            $this->event->register('cron.run', 'marketing/cron');

            $events = $db->query('Select * from #__event')->rows;

            foreach ($events as $_event) {
                $this->event->register($_event['trigger'], $_event['action']);
            }
            
           
        }

        define('HTTP_SERVER', $config->get('config_url'));
        define('HTTPS_SERVER', $config->get('config_ssl'));
        define('HTTP_CATALOG', $config->get('config_catalog'));
        define('HTTPS_CATALOG', $config->get('config_catalog_ssl'));


        require_once('Log.php');
        $log = new \Core\Log(DATE("Ymd") . '_error_log.txt');
        self::$registry->set('log', $log);
        set_error_handler(array($this, 'error_handler'));
        set_exception_handler(array($this, 'exception_handler'));





        require_once('Loader.php');
        $loader = new \Core\Loader();
        self::$registry->set('load', $loader);

        require_once('Url.php');
        $url = new \Core\Url(HTTP_SERVER, $config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER);
        self::$registry->set('url', $url);

        require_once('Request.php');
        $request = new \Core\Request();
        self::$registry->set('request', $request);

        // Response
        require_once('Response.php');
        $response = new \Core\Response();
        $response->addHeader('Content-Type: text/html; charset=utf-8');
        $response->addHeader('X-Powered-By: CoreFW');
        $response->setCompression($config->get('config_compression'));
        self::$registry->set('response', $response);

        // Cache
        require_once('Cache.php');
        $cache = new \Core\Cache();
        self::$registry->set('cache', $cache);

        // Session
        require_once('Session.php');
        $session = new \Core\Session();
        self::$registry->set('session', $session);

        //Language
        require_once('Language.php');
        $language = new \Core\Language($config->get('config_language'));
        $language->load($config->get('config_language'));
        self::$registry->set('language', $language);

        // Document
        require_once('Document.php');
        require_once('Template.php');
        require_once('Encryption.php');
        self::$registry->set('document', new \Core\Document());
        self::$registry->set('template', new \Core\Template());
// Encryption
        self::$registry->set('encryption', new \Core\Encryption($config->get('config_encryption')));


        date_default_timezone_set(self::$registry->get('config')->get('config_gttimezone'));

        //Get all the plugins!!!
        if ($ns != 'installer') {
            \Core\Shortcode::init($ns);
        }
    }

    public static function autoload($className) {

        $thisClass = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);

        $baseDir = __DIR__;

        if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
            $baseDir = substr($baseDir, 0, -strlen($thisClass));
        }

        $className = ltrim($className, '\\');

        $fileName = $baseDir;
        $fileName2 = dirname($baseDir) . '/vendor/';

        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            $fileName2 .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $fileName2 .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (is_file($fileName)) {

            require(__modification($fileName));
        } elseif (is_file($fileName2)) {
            require $fileName2;
        } else {
            //   echo '<pre>';
            //    debug_print_backtrace();
            //   trigger_error("Could not load class $className ");
        }
    }

    public static function registerAutoloader() {
        spl_autoload_register(__NAMESPACE__ . "\\Core::autoload");
    }

    /**
     * 
     * @param \Core\Exception $exception
     */
    function exception_handler($exception) {
        
        
        $config = self::$registry->get('config');
        $log = self::$registry->get('log');

        if ($config->get('config_error_display') || NS == 'installer') {
            //FLUSH THE OUPUT
            @ob_end_clean();
            echo '<b>' . "Uncaught exception: </b>" . $exception->getMessage() . '<br />';
            debugPre($exception->getTrace());
        }

        if ($config->get('config_error_log')) {
            $log->write('PHP Exeption' . "Uncaught exception: " . $exception->getMessage());
        }
        exit;
    }

    public function error_handler($errno, $errstr, $errfile = '', $errline = 0) {

        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }
        $config = self::$registry->get('config');
        $log = self::$registry->get('log');

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        if ($config->get('config_error_display') || (NS == 'installer' && $error != 'Notice')) {
            echo '<b>#' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b><br />';
        }

        if ($config->get('config_error_log')) {
            $log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline . ' : ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }

        return true;
    }

    public function addPreAction($action) {
        $this->pre_actions[$action] = $action;
        return $this;
    }

    public function removePreAction($action) {
        unset($this->pre_actions[$action]);
        return $this;
    }

    public function setDefaultRoute($route) {
        $this->default_route = $route;
        return $this;
    }

    public function setErrorRoute($route) {
        $this->error_route = $route;
        return $this;
    }

    public function dispatch() {

        $this->registry->set('default_route', $this->default_route);

        // Front Controller 
        $controller = new \Core\Front();
        foreach ($this->pre_actions as $pre_action) {
            $controller->addPreAction(new \Core\Action($pre_action));
        }

        if (isset($this->request->get['p'])) {
            $action = new \Core\Action($this->request->get['p']);
        } else {
            $action = new \Core\Action($this->default_route);
        }


        $controller->dispatch($action, new \Core\Action($this->error_route));

// Output
        $this->response->output();
    }

    public function dispatch_cli() {

        @ini_set("memory_limit", "128M");

        // Front Controller 
        $controller = new \Core\Front();
        foreach ($this->pre_actions as $pre_action) {
            $controller->addPreAction(new \Core\Action($pre_action));
        }

        if (isset($this->request->get['p'])) {
            $action = new \Core\Action($this->request->get['p']);
        } else {
            $action = new \Core\Action('cron/error');
        }

        $controller->dispatch($action, new \Core\Action('cron/error'));

// Output
        $this->response->output();
    }

    /**
     * Returns object from registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        if ($key == 'registry') {
            return self::$registry;
        }
        return self::$registry->get($key);
    }

    /**
     * Sets object to the registry
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        self::$registry->set($key, $value);
    }

}
