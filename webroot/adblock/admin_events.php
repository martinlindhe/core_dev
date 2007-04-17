<?
	require_once('config.php');

	$session->requireAdmin();

	require('design_head.php');

	$db->showEvents();

	require('design_foot.php');
?>