<?
	require_once('find_config.php');

	$session->requireAdmin();

	if (!empty($_POST['title']) && !empty($_POST['body']) && !empty($_POST['publish']) ) {
		addNews($_POST['title'], $_POST['body'], $_POST['publish'], $_POST['rss']);
	}
	
	include($project.'design_head.php');

	echo '<h1>Add news</h1>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].getProjectPath(false).'">';
	echo '<input type="hidden" name="rss" value="0"/>';
	echo 'Title:<br/>';
	echo '<input type="text" name="title" size="50"/><br/>';
	echo 'Text:<br/>';
	echo '<textarea name="body" cols="60" rows="16"></textarea><br/>';
	echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1" checked="checked"/>';

	echo '<label for="rss_check">';
	echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
	echo 'Include this news in the RSS feed</label><br/><br/>';
	echo 'Time for publication:<br/>';
	echo '<input type="text" name="publish" value="'.date('Y-m-d H:i').'"/> ';
	echo '<input type="submit" class="button" value="Store news"/><br/>';
	echo '</form>';

	include($project.'design_foot.php');
?>