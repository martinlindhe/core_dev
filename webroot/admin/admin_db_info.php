<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	$db->showConfig();

	require($project.'design_foot.php');
?>