<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
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
		$show = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Ser p&aring; sina fotoalbum');
	} else {
		setUserStatus($db, 'Ser p&aring; '.$niceshowname.' fotoalbum');
		logVisitor($db, $show);
	}

	/* Create a new file category */
	if (!empty($_POST['catname'])) {
		$catId = addFileCategory($db, FILETYPE_PHOTOALBUM, $_POST['catname']);
		if ($catId) {
			header('Location: photoalbums_show.php?id='.$catId);
			die;
		}
	}

	include('design_head.php');
	include('design_user_head.php');

		$content =
			'<b>Lage et nytt fotoalbum:</b><br><br>';
		
		$content .=
			'<form method="post" name="newcat" action="'.$_SERVER['PHP_SELF'].'?id='.$show.'">'.
			'Navn p&aring; albumet:<br>'.
			'<input type="text" name="catname" size=40><br><br>'.
			'<input type="submit" class="button" value="Lage fotoalbum">'.
			'</form>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$show.'">Fotoalbum</a>|Lage fotoalbum', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');
	
	if (!empty($file_uploaded)) {
		JS_Alert('Filen har sparats!');
	}
?>