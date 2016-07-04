<?php

define('NS', 'front');
define('VERSION', '1.8.0');
error_reporting(E_ALL);

if (!is_file('config.php')) {
    header("location: install/");
    exit;
}
require('config.php');
if (!defined('DIR_APPLICATION')) {
    header('Location: install/index.php');
    exit;
}

require(DIR_SYSTEM . 'startup.php');

require_once(DIR_ROOT . 'system/Core/Core.php');
$core = new \Core\Core($config);
$core->load->library('customer');
$core->customer = new Customer();
$currency = new \Core\Currency();
$core->currency = $currency;

if(is_file(DIR_VENDOR . 'autoload.php')){
 require(DIR_VENDOR . 'autoload.php');
}


//EVENT DISPATCH


$core->event->trigger("core.pre.dispatch", $core);


if(BASE_REQUEST_TYPE == 'cli'){
    $core->dispatch_cli();
}else{

$core->addPreAction('common/maintenance');
$core->addPreAction('common/home/marketing');

//Should always be last!!! NB
$core->addPreAction('common/seo_url');
$core->dispatch();
}
