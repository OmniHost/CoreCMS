<?php

define('NS', 'front');
define('VERSION', '1.2.0');
error_reporting(E_ALL);

if(!is_file('config.php')){
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
$core->addPreAction('common/maintenance');
$core->addPreAction('common/seo_url');
$core->addPreAction('common/home/marketing');
$core->dispatch();
