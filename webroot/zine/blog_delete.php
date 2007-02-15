<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($db, $blogId);
	if ($_SESSION['userId'] != $blog['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_GET['confirmed'])) {
		deleteBlog($db, $blogId, $_SESSION['userId']);
		header('Location: blogs.php');
		die;
	}

	include('design_head.php');
	include('design_user_head.php');
	
		$content = $config['blog']['text']['delete_blog_confirm'].' <b>'.$blog['blogTitle'].'</b>?<br><br>';
		$content .= '<table width="100%"><tr>';
		$content .= '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'&confirmed">'.$config['text']['prompt_yes'].'</a></td>';
		$content .= '<td align="center"><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>';
		$content .= '</tr></table>';

		echo '<div id="user_blog_content">';
		echo MakeBox('<a href="blogs.php">'.$config['blog']['text']['blogs'].'</a>|'.$config['blog']['text']['delete_blog'], $content);
		echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>