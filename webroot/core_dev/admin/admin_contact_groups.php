<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>Contact groups</h1>';
	
	echo 'Existing contact groups: '.getCategoriesSelect(CATEGORY_CONTACT);
	
	makeNewCategoryDialog(CATEGORY_CONTACT);

	require($project.'design_foot.php');
?>