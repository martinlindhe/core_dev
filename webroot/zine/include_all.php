<?
	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$time_start = microtime(true);

	include_once('config.php');

	ContinueSession($db);
?>