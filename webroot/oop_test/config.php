<?
	$time_start = microtime(true);
	
	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'functions/class.DB_MySQLi.php');
	require_once($config['core_root'].'functions/class.Session.php');
	require_once($config['core_root'].'functions/class.Files.php');

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbOOPHP';
	$config['database']['debug']		= true;

	/* A variable named $db must exist for all future functions to work. */
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*12;
	$config['session']['name'] = 'OOPtest';
	$config['session']['sha1_key'] = 'sitecode_uReply';		//todo: byt ut sitecode

	/* A variable named $session must exist for all future functions to work. */
	$session = new Session($config['session']);
	
	$config['files']['upload_dir'] = 'E:/Devel/webupload_ooptest/';
	$config['files']['thumbs_dir'] = 'E:/Devel/webupload_ooptest/thumbs/';
	$files = new Files($config['files']);
?>