<?
	error_reporting(E_ALL);

	$config['core_root'] = '../../';

	set_include_path($config['core_root'].'core/');
	require_once('functions_validate_ssn.php');
	require_once('functions_general.php');
	require_once('functions_textformat.php');
	restore_include_path();

	$config['debug'] = true;
?>