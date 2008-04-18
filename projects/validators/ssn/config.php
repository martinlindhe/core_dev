<?
	error_reporting(E_ALL);

	$config['core']['fs_root'] = '../../';

	set_include_path($config['core']['fs_root'].'core/');
	require_once('functions_validate_ssn.php');
	require_once('functions_general.php');
	require_once('functions_textformat.php');
	restore_include_path();

	$config['debug'] = true;
?>
