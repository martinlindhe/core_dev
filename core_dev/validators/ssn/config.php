<?
	error_reporting(E_ALL);

	$config['core_root'] = '/home/martin/dev/martin/webroot/core_dev/';
	$config['core_web_root'] = '/core_dev/';

	$config['web_root'] = '/core_dev/validators/ssn/';
	$config['default_title'] = 'id validator / generator';

	set_include_path($config['core_root'].'core/');
	require_once('functions_validate_ssn.php');
	require_once('functions_general.php');
	require_once('functions_textformat.php');
	restore_include_path();

	$config['debug'] = true;
?>