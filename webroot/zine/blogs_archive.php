<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$show = '';
	if (isset($_GET['id'])) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else {
		$show = $session->id;
		$showname = $session->username;
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	require('design_head.php');

	$show_year = $_GET['y'];
	$show_month = $_GET['m'];

		$content = 'Archive for '.$show_month.' '.$show_year.'<br/><br/>';

		$list = getBlogsByMonth($show, $show_month, $show_year);
		for ($i=0; $i<count($list); $i++) {
			$content .= $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a><br/>';
		}
		
		if (!count($list)) {
			$content .= '<div class="critical">No archive for specified month.</div>';
		}

		echo $content;

	require('design_foot.php');
?>