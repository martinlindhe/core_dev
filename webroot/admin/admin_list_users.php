<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');

	echo 'list users<br/><br/>';
	
	$mode = 0;
	if (!empty($_GET['mode'])) $mode = $_GET['mode'];
	$list = getUsers($mode);
	d($list);

	require($project.'design_foot.php');
?>