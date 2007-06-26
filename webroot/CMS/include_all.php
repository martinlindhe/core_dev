<?
	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');

	$time_start = microtime(true);

	include_once('config.php');

	ContinueSession($db);
?>