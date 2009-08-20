<?php

error_reporting(E_ALL);

$config['core']['fs_root'] = '/home/martin/dev/martin/webroot/core_dev/';
$config['core']['web_root'] = '/core_dev/';

$config['app']['web_root'] = '/core_dev/validators/cc/';
$config['default_title'] = 'credit card validator';

set_include_path($config['core']['fs_root'].'core/');
require_once('functions_general.php');
require_once('validate_cc.php');
restore_include_path();

$config['debug'] = true;
?>
