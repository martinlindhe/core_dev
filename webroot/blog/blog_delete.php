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
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	echo '&Auml;r du s&auml;ker p&aring; att du vill radera bloggen <b>'.$blog['blogTitle'].'</b>?<br><br>';

	echo '<table width="300"><tr>';
	echo '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'&confirmed">'.$config['text']['prompt_yes'].'</a></td>';
	echo '<td align="center"><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>';
	echo '</tr></table>';

	include('design_foot.php');
?>