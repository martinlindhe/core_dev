<?php

$time_start = microtime(true);

error_reporting(E_ALL);

$config['core']['fs_root'] = '/home/ml/dev/core_dev/';
$config['core']['web_root'] = '/core_dev/';

$config['app']['web_root'] = '/lang/';
$config['default_title'] = 'lang project';

set_include_path($config['core']['fs_root'].'core/');
require_once('class.DB_MySQLi.php');
require_once('class.Auth_Standard.php');
require_once('class.Users.php');
require_once('class.Session.php');
require_once('class.Files.php');
require_once('functions_general.php');
require_once('functions_wiki.php');
restore_include_path();

require_once('functions_lang.php');

$config['debug'] = true;

$config['database']['username']	= 'root';
$config['database']['password']	= '';
$config['database']['database']	= 'dbLang';
$db = new DB_MySQLi($config['database']);

$config['session']['timeout'] = (60*60)*24*7;	//7 days
$config['session']['name'] = 'langID';
$session = new Session($config['session']);

$config['auth']['sha1_key'] = 'sdalkj8vkjncjksdSdFsdfg70kcvvcvGFzadeg5ae5h';
$config['auth']['allow_registration'] = true;
$config['auth']['userdata'] = false;
$auth = new Auth_Standard($config['auth']);

?>
