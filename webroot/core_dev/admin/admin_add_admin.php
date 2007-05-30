<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');
	echo createMenu($super_admin_menu, 'blog_menu');
?>

	todo - kunna skapa en ny user som e admin/super admin & kunna promota en vanlig user till admin/super admin
	
<?
	require($project.'design_foot.php');
?>