<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin edit todo list categories<br/><br/>';
	
	manageCategoriesDialog(CATEGORY_TODOLIST);

	echo '<a href="admin_current_work.php">Back to current work</a>';

	require($project.'design_foot.php');
?>