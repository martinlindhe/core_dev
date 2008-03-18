<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	echo createMenu($super_admin_menu, 'blog_menu');
	echo createMenu($super_admin_tools_menu, 'blog_menu');

	echo '<h1>Manage contacts</h1>';

	echo 'Here you can create/modify the contact types that users can classify their friends in.<br/><br/>';
	
	manageCategoriesDialog(CATEGORY_CONTACT);

	require($project.'design_foot.php');
?>
