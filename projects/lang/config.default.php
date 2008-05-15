<?php

$time_start = microtime(true);

error_reporting(E_ALL);

$config['core']['fs_root'] = '../core_dev/';
$config['core']['web_root'] = '/core_dev/';

$config['app']['web_root'] = '/lang/';
$config['default_title'] = 'lang project';

set_include_path($config['core']['fs_root'].'core/');
require_once('class.DB_MySQLi.php');
require_once('class.Session.php');
require_once('class.Files.php');
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
$config['session']['sha1_key'] = 'sdalkj8vkjncjksdSdFsdfg70kcvvcvGFzadeg5ae5h';
$config['session']['allow_registration'] = true;
$config['session']['reserved_usercheck'] = false;
$config['session']['userdata'] = false;
$session = new Session($config['session']);

$config['files']['apc_uploads'] = false;
$config['files']['upload_dir'] = 'E:/devel/webupload/sample/';
$config['files']['thumbs_dir'] = 'E:/devel/webupload/sample/thumbs/';
$files = new Files($config['files']);

$config['wiki']['allow_html'] = true;
$config['wiki']['allow_files'] = true;

$session->handleSessionActions();
?>
