<?
	//kunna se nyheter som ska publiceras i framtiden

	require_once('find_config.php');

	$session->requireAdmin();

	include($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>Manage news</h1>';

	echo '<h2>Unpublished news</h2>';
	$list = getUnpublishedNews();
	foreach ($list as $row)
	{
		echo $row['timeToPublish'].':<br/>';
		echo '<a href="'.$config['web_root'].'news.php?News:'.$row['newsId'].'">'.$row['title'].'</a><br/>';
	}

	echo '<h2>Published news</h2>';
	$list = getPublishedNews();
	foreach ($list as $row)
	{
		echo $row['timeToPublish'].':<br/>';
		echo '<a href="'.$config['web_root'].'news.php?News:'.$row['newsId'].'">'.$row['title'].'</a><br/>';
	}
	
	echo '<a href="admin_news_add.php'.getProjectPath(0).'">Add news</a>';
	
	include($project.'design_foot.php');
?>