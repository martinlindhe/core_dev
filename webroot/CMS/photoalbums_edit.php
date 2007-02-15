<?
	//id = albumId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$albumId = $_GET['id'];
	$album = getFileCategory($db, FILETYPE_PHOTOALBUM, $albumId);
		
	if ($album['ownerId'] != $_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_POST['name'])) {
		setFileCategoryName($db, FILETYPE_PHOTOALBUM, $albumId, $_POST['name']);
		$album = getFileCategory($db, FILETYPE_PHOTOALBUM, $albumId);
	}

	$album_name = $album['categoryName'];

	include('design_head.php');
	include('design_user_head.php');

		$content =
			'<b>Redigere fotoalbumet</b><br><br>'.
			'Her kan du endre innstillinger for amlbumet <b>'.$album_name.'</b>.<br><br>';

		$content .=
			'<form method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?id='.$albumId.'">'.
			'Navn p&aring; albumet:<br>'.
			'<input type="text" name="name" size=50 value="'.$album_name.'"><br><br>'.
			'<input type="submit" class="button" value="'.$config['text']['link_save'].'"><br>'.
			'</form>';
			
		$content .= '<br><br><a href="photoalbums_show.php?id='.$albumId.'">Vise albumet</a>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$album['ownerId'].'">Fotoalbum</a>|'.$config['text']['link_edit'], $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');
	
	if (isset($file_uploaded) && !$file_uploaded) {
		JS_Alert('Problem vid uppladdning. Endast bildfiler &auml;r till&aring;tna');
	}
?>