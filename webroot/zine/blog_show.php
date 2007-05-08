<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($blogId);
	if (!$blog) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');

	if ($session->id == $blog['userId']) {
		echo '<a href="blog_edit.php?id='.$blogId.'">Edit blog</a><br/><br/>';
	} else {
		echo '<a href="blog_report.php?id='.$blogId.'">Report blog</a><br/><br/>';
	}

	echo '<span style="font-size:16px; font-weight:bold;">'.$blog['blogTitle'].'</span><br/>';
	if ($blog['categoryId']) echo '(in the category <b>'.$blog['categoryName'].'</b>)<br/><br/>';
	else echo ' (no category)<br/><br/>';

	echo 'Published '. $blog['timeCreated'].' by '.nameLink($blog['userId'], $blog['userName']).'<br/>';
	if ($blog['timeUpdated']) {
		echo '<b>Updated '. $blog['timeUpdated'].'</b><br/>';
	}

	echo '<br>';
	echo '<div class="blog">'.formatUserInputText($blog['blogBody']).'</div>';

	require('design_foot.php');
?>