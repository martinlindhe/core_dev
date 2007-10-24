<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>Manage contact groups</h1>';
	
	manageCategoriesDialog(CATEGORY_CONTACT);

	require($project.'design_foot.php');
?>