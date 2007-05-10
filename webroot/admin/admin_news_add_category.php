<?
	require_once('find_config.php');

	$session->requireAdmin();

	if (!empty($_POST['title']) ) {
		addCategory(CATEGORY_NEWS, $_POST['title']);
	}
	
	include($project.'design_head.php');

	echo '<h1>Create news category</h1>';

	echo 'The following categories already exist: ';
	echo getCategoriesSelect(CATEGORY_NEWS, 'news_cat').'<br/><br/>';


	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].getProjectPath(false).'">';
	echo 'Title:<br/>';
	echo '<input type="text" name="title" size="50"/><br/>';
	echo '<input type="submit" class="button" value="Create"/><br/>';
	echo '</form>';

	include($project.'design_foot.php');
?>