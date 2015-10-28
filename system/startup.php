<?php

define('DIR_UPLOAD', DIR_SYSTEM . 'upload/');
define('DIR_MODIFICATION', DIR_SYSTEM . 'modification/');

//require_once(DIR_SYSTEM . 'helper/xhprof.php');
// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
    exit('PHP5.3+ Required');
}

// Register Globals
if (ini_get('register_globals')) {
    ini_set('session.use_cookies', 'On');
    ini_set('session.use_trans_sid', 'Off');

    session_set_cookie_params(0, '/');
    session_start();

    $globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

    foreach ($globals as $global) {
        foreach (array_keys($global) as $key) {
            unset(${$key});
        }
    }
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {

    function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[clean($key)] = clean($value);
            }
        } else {
            $data = stripslashes($data);
        }

        return $data;
    }

    $_GET = clean($_GET);
    $_POST = clean($_POST);
    $_REQUEST = clean($_REQUEST);
    $_COOKIE = clean($_COOKIE);
}

if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}
/*
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
  define('BASE_REQUEST_TYPE', 'ajax');
  } else {
  define('BASE_REQUEST_TYPE', strtolower($_SERVER['REQUEST_METHOD']));
  }
 */
$sapi = php_sapi_name();
if (in_array($sapi, array('cli', 'cgi', 'cgi-fcgi')) && empty($_SERVER['REMOTE_ADDR'])) {
    define('BASE_REQUEST_TYPE', 'cli');
} else if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    define('BASE_REQUEST_TYPE', 'ajax');
} else {
    define('BASE_REQUEST_TYPE', strtolower($_SERVER['REQUEST_METHOD']));
}

$cli_params = false;
if (BASE_REQUEST_TYPE == 'cli') {



    if ($_SERVER['argc'] >= 3) {

        if (basename($_SERVER['argv'][0]) == 'cli.php' || basename($_SERVER['argv'][0]) == 'index.php'  ) {
            $sn = $_SERVER['argv'][1];
            //$p = $_SERVER['argv'][2];
            $route = array();
            $args = $_SERVER['argv'];
            unset($args[0], $args[1]);
            foreach ($args as $arg) {
                $route[] = $arg;
            }
            $p = implode("&", $route);

            parse_str($p, $_GET);
            $_SERVER['REQUEST_METHOD'] = "GET";
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            $_SERVER['SERVER_PORT'] = '80';
            $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $sn;
            $cli_params = $_SERVER['REQUEST_URI'] = $p;
            set_time_limit(0);
        }
    }
    if (!$cli_params) {
        die('Oops - there was a problem processing the CLI request.');
    }
}

// Windows IIS Compatibility  
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Helpers
require_once(DIR_SYSTEM . 'helper/json.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');
require_once(DIR_SYSTEM . 'helper/helpers.php');
require_once(DIR_SYSTEM . 'helper/simplehtmldom.php');


