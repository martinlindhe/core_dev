<?
	$time_start = microtime(true);

	error_reporting(E_ALL);

	$config['core_root'] = '../core_dev/';
	$config['core_web_root'] = '/core_dev/';

	$config['web_root'] = '/id_generator/';
	$config['default_title'] = 'id generator';

	set_include_path($config['core_root'].'core/');
	require_once('class.Session.php');
	require_once('functions_general.php');
	restore_include_path();

	$config['debug'] = true;

	$session = new Session();
?>