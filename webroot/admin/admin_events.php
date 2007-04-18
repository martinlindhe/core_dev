<?
	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');

	$db->showEvents();

	require($project.'design_foot.php');
?>