<?php

error_reporting(E_ALL);
$time_start = microtime(true);
$config['debug'] = true;

$config['core']['fs_root'] = '/home/martin/dev/core_dev/';
$config['core']['web_root'] = '/core_dev/';
$config['core']['full_url'] = 'http://process.icn.se:2000'.$config['core']['web_root'];

$config['app']['web_root'] = '/';
$config['app']['full_url'] = 'http://process.icn.se:2000'.$config['app']['web_root'];
$config['default_title'] = 'process server project';					//default title for pages if no title is specified for that page

$config['language'] = 'se';

set_include_path($config['core']['fs_root'].'core/');
require_once('class.DB_MySQLi.php');
require_once('class.Auth_Standard.php');
require_once('class.Session.php');
require_once('class.Files.php');
require_once('functions_core.php');
require_once('functions_wiki.php');
require_once('functions_process.php');
restore_include_path();

$config['db']['username']	= 'ml';
$config['db']['password']	= 'nutana';
$config['db']['database']	= 'dbProcess';
$config['db']['host']	= 'process1.x';
$config['db']['port']	= 44000;
$db = new DB_MySQLi($config['db']);

$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
$config['session']['name'] = 'coreID';
$config['session']['allow_themes'] = true;
$session = new Session($config['session']);

$config['auth']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
$config['auth']['allow_login'] = true;
$config['auth']['allow_registration'] = false;
$config['auth']['userdata'] = false;
$auth = new Auth_Standard($config['auth']);

$config['files']['apc_uploads'] = false;
$config['files']['upload_dir'] = '/home/martin/process-uploads/';
$files = new Files($config['files']);

$config['wiki']['allow_html'] = true;
$config['wiki']['allow_files'] = true;
?>
