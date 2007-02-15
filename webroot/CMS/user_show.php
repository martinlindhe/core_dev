<?
	//id specifies userId

	include('include_all.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		die;
	}
	
	$show = $_GET['id'];
	//$showname = getUserName($db, $show);
	$showname = getUserdataByFieldname($db, $show, 'Nickname');
	if (!$showname) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Ser p&aring; sin side');
	} else {
		setUserStatus($db, 'Ser p&aring; '.$niceshowname.' side');
		logVisitor($db, $show);
	}

	include('design_head.php');
	include('design_user_head.php');

		$profil = getUserSetting($db, $show, 'presentation');
		$profil = nl2br($profil);
		if (!$profil) {
			if ($_SESSION['userId'] == $show) {
				$profil = 'Du har ikke laget noen presentasjon. <a href="user_edit_presentation.php">Klik her</a> for &aring; lage en.';
			} else {
				$profil = 'Brukeren har ikke laget noen presentasjon.';
			}
		}

		$content  = '<div id="user_presentation_text">'.$profil.'</div><br>';

		$content .= '<b>Brukerens siste innlegg</b><br>';
		$content .= displayUsersLatestPosts($db, $show, 10);

		echo '<div id="user_presentation_content">';
			echo MakeBox($niceshowname.' sida', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>