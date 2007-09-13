<?
	error_reporting(E_ALL);

	$config['core_root'] = '/home/martin/dev/webroot/core_dev/';

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	restore_include_path();

	require_once('functions_dylogic.php');
	
	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravel';
	$config['database']['database']	= 'dbM2W';
	$db = new DB_MySQLi($config['database']);
	
	storeCDR();
?>
