<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	phpinfo();

	require($project.'design_foot.php');
?>