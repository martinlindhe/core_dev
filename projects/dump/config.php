<?php
	//xdebug_enable();
	error_reporting(E_ALL);
	$time_start = microtime(true);
	$config['debug'] = true;

	$config['core']['fs_root'] = 'C:/devel/webroot/core_dev/';
	$config['core']['web_root'] = '/core_dev/';

	$config['app']['web_root'] = '/projects/dump/';
	$config['default_title'] = 'dump';					//default title for pages if no title is specified for that page

	set_include_path($config['core']['fs_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	restore_include_path();

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbDump';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'coreID';
	$config['session']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
	$config['session']['allow_login'] = true;
	$config['session']['allow_registration'] = true;
	$config['session']['userdata'] = false;
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['anon_uploads'] = true;
	$config['files']['upload_dir'] = 'C:/devel/webupload/dump/';
	$config['files']['thumbs_dir'] = $config['files']['upload_dir'].'thumbs/';
	$files = new Files($config['files']);

	$session->handleSessionActions();
?>
