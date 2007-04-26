<?
	require_once('find_config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$newsId = $_GET['id'];

	$session->requireAdmin();

	require($project.'design_head.php');

	if (!empty($_POST['title'])) {
		updateNews($newsId, $_POST['title'], $_POST['body'], strtotime($_POST['publish']), $_POST['rss']);
	}

	$item = getNewsItem($newsId);

	echo '<h1>Edit news article</h1>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$newsId.getProjectPath().'">';
	echo '<input type="hidden" name="rss" value="0"/>';
	echo 'Title: <input type="text" name="title" size="50" value="'.$item['title'].'"/><br/>';
	echo 'Text:<br/>';
	echo '<textarea name="body" cols="60" rows="16">'.$item['body'].'</textarea><br/>';
	echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1"'.($item['rss_enabled']?' checked="checked"':'').'/>';
	echo '<label for="rss_check">';
	echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
	echo 'Include this news in the RSS feed</label><br/><br/>';
	echo 'Time for publication:<br/>';
	echo '<input type="text" name="publish" value="'.$item['timeToPublish'].'"/> ';
	echo '<input type="submit" class="button" value="Save changes"/><br/>';
	echo '</form><br/>';
		
	//echo '<a href="admin_news.php?id='.$item['newsId'].getProjectPath().'">Show this news</a><br/>';
	//echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$item['newsId'].getProjectPath().'">Delete this news</a>';

	require($project.'design_foot.php');
?>