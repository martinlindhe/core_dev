<?
	require_once('find_config.php');

	$session->requireAdmin();

	if (!empty($_POST['title']) ) {
		//addNews($_POST['title'], $_POST['body'], $_POST['publish'], $_POST['rss']);
		addCategory(CATEGORY_NEWS, $_POST['title']);
	}
	
	include($project.'design_head.php');

	echo '<h1>Create news category</h1>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].getProjectPath(false).'">';
	echo 'Title:<br/>';
	echo '<input type="text" name="title" size="50"/><br/>';
	echo '<input type="submit" class="button" value="Create"/><br/>';
	echo '</form>';

	include($project.'design_foot.php');
?>