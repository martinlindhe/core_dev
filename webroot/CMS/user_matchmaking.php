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
		setUserStatus($db, 'Spanar in sina likasinnade');
	} else {
		setUserStatus($db, 'Spanar in '.$niceshowname.' likasinnade');
	}


	include('design_head.php');

		echo nameLink($show, ucfirst($niceshowname).' sida').' - Likasinnade<br><br>';

		echo 'H&auml;r &auml;r '.nameLink($show, $niceshowname).' 5 mest likasinnade.<br><br>';
		
		/* Bästa matchningarna i matchmaking */
		echo displayBestMatchmakes($db, $show, $_SESSION['userId'], 5);

	include('design_foot.php');

?>