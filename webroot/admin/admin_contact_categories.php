<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'contact categories..<br/>';

	require($project.'design_foot.php');
?>