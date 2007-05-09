<?
	//fixme: använd standard-are-you-sure funktionen

	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['session']['home_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($blogId);
	if ($session->id != $blog['userId']) {
		header('Location: '.$config['session']['home_page']);
		die;
	}

	if (isset($_GET['confirmed'])) {
		deleteBlog($blogId, $session->id);
		header('Location: blogs.php');
		die;
	}

	require('design_head.php');
	
	echo 'Are you sure you want to delete this blog? <b>'.$blog['blogTitle'].'</b>?<br><br>';
	echo '<table width="100%"><tr>';
	echo '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'&confirmed">Yes, im sure</a></td>';
	echo '<td align="center"><a href="javascript:history.go(-1);">No</a></td>';
	echo '</tr></table>';

	require('design_foot.php');
?>