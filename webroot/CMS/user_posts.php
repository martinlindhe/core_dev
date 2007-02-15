<?
	include('include_all.php');
	
	$show = '';
	if (!empty($_GET['id']) && $_GET['id'] != $_SESSION['userId']) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else if ($_SESSION['userId']) {
		$show     = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}
	
	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname."s";
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Spanar in sina inl&auml;gg');
	} else {
		setUserStatus($db, 'Spanar in '.$niceshowname.' inl&auml;gg');
	}


	include('design_head.php');
	
	echo nameLink($show, ucfirst($niceshowname).' sida').' - Senaste inl&auml;ggen<br><br>';
	echo 'H&auml;r ser du '.nameLink($show, $niceshowname).' 10 senaste debattinl&auml;gg.<br><br>';
	echo displayUsersLatestPosts($db, $show, 10).'<br>';

	include('design_foot.php');

?>