<?
	error_reporting(E_ALL);

	$config['core_root'] = '/home/martin/dev/martin/webroot/core_dev/';
	$config['core_web_root'] = '/core_dev/';

	$config['web_root'] = '/core_dev/validators/cc/';
	$config['default_title'] = 'credit card validator';

	set_include_path($config['core_root'].'core/');
	require_once('functions_validate_cc.php');
	require_once('functions_general.php');
	restore_include_path();

	$config['debug'] = true;
?>