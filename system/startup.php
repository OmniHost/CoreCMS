<?php

if (!defined('DIR_UPLOAD')) {
    define('DIR_UPLOAD', DIR_SYSTEM . 'upload/');
    define('DIR_MODIFICATION', DIR_SYSTEM . 'modification/');
}
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

    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['SERVER_PORT'] = '80';
    $_SERVER['DOCUMENT_ROOT'] = rtrim(DIR_ROOT, '/');
    $_SERVER['HTTP_USER_AGENT'] = 'PHP SHELL CLI';
    $_SERVER['SCRIPT_FILENAME'] = DIR_ROOT . 'index.php';


    $cli_options = getopt("p:g:", array("host:"));

    $host = !empty($cli_options['host']) ? $cli_options['host'] : $config['config_url'];
    $p = !empty($cli_options['p']) ? $cli_options['p'] : 'common/cron';
    $g = !empty($cli_options['g']) ? $cli_options['g'] : false;


    $host = ltrim($host, 'http://');
    $host = ltrim($host, 'https://');
    $host = rtrim($host, '/');
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $host;



    if ($g) {
        parse_str($g, $_GET);
    }

    if ($p) {
        $_GET['p'] = $p;
    }
    $_SERVER['QUERY_STRING'] = http_build_query($_GET);
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


