<?php
define('VERSION', '1.0.1');
define('NS','installer');

define('APP_NAMESPACE', 'installer');

// Error Reporting
error_reporting(E_ALL);

// HTTP
define('HTTP_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/.\\') . '/');
define('HTTP_CORECMS', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(rtrim(dirname($_SERVER['SCRIPT_NAME']), 'install'), '/.\\'). '/');

// DIR
define('DIR_ROOT', str_replace('\'', '/', realpath(dirname(dirname(__FILE__)))) . '/');
define('DIR_APPLICATION', DIR_ROOT . 'install/');
define('DIR_SYSTEM', DIR_ROOT . 'system/');
define('DIR_DATABASE', DIR_SYSTEM . 'database/');
define('DIR_CORE', DIR_SYSTEM . 'Core/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_MODIFICATION', DIR_SYSTEM . 'modification/');


// Startup
error_reporting(E_ALL);

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
		foreach(array_keys($global) as $key) {
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


// Helper
require_once(DIR_SYSTEM . 'helper/json.php'); 
require_once(DIR_SYSTEM . 'helper/utf8.php'); 
require_once(DIR_SYSTEM . 'helper/helpers.php');

require_once(DIR_ROOT . 'system/Core/Core.php');
$core = new \Core\Core(array(), 'installer');
$core->default_route = 'step_1';

// Upgrade
$upgrade = false;


if (file_exists(DIR_ROOT . 'config.php')) {
    if (filesize(DIR_ROOT . '/config.php') > 0) {
		$upgrade = true;
        $core->default_route = 'upgrade';
		
        $lines = file(DIR_ROOT . 'config.php');
		
		foreach ($lines as $line) {
			if (strpos(strtoupper($line), 'DB_') !== false) {
                $parts = explode("=>", $line);
                $_k = str_replace(array("'", '"'), "", trim($parts[0]));
                $_v = str_replace(array("'", '"'), "", trim($parts[1]));
                $core->config->set($_k,$_v);
			}
		}
	}
}
$core->dispatch();
