<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	$session->showInfo();

	require($project.'design_foot.php');
?>