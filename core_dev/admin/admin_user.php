<?
	require_once('find_config.php');
	$session->requireAdmin();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$userId = $_GET['id'];

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>User admin for '.Users::getName($userId).'</h1>';

	showComments(COMMENT_USER, $userId);

	require($project.'design_foot.php');
?>