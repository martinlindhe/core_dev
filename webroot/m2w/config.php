<?
	error_reporting(E_ALL);

	$config['core_root'] = '/home/martin/dev/webroot/core_dev/';

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	restore_include_path();

	require_once('functions_dylogic.php');
	
	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravel';
	$config['database']['database']	= 'dbM2W';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'm2wID';
	$session = new Session($config['session']);

	$config['vxml']['service'] = M2W_CHATROOM;
?>
