<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin feedback<br/><br/>';
	
	$list = getFeedback();
	d($list);

	require($project.'design_foot.php');
?>