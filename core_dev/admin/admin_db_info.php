<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	echo createMenu($super_admin_menu, 'blog_menu');

	$db->showConfig();

	require($project.'design_foot.php');
?>