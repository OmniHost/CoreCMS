<?php

define('VERSION', '1.0.1');
define('NS','admin');
error_reporting(E_ALL);

require('config.php');
require(DIR_SYSTEM . 'startup.php');


require_once(DIR_ROOT . 'system/Core/Core.php');
$core = new \Core\Core($config, 'admin');
$core->user = new \Core\User();

$core->addPreAction('user/login/status');
$core->addPreAction('user/login/permission');

$core->dispatch();
