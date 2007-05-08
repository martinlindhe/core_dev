<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($blogId);
	if ($session->id != $blog['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_GET['confirmed'])) {
		deleteBlog($blogId, $session->id);
		header('Location: blogs.php');
		die;
	}

	include('design_head.php');
	
		$content = 'Are you sure you want to delete this blog? <b>'.$blog['blogTitle'].'</b>?<br><br>';
		$content .= '<table width="100%"><tr>';
		$content .= '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'&confirmed">Yes, im sure</a></td>';
		$content .= '<td align="center"><a href="javascript:history.go(-1);">No</a></td>';
		$content .= '</tr></table>';

		echo $content;

	include('design_foot.php');
?>