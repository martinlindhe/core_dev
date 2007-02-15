<?
	//id = albumId

	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$albumId = 0;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$albumId = $_GET['id'];
		$album = getFileCategory($db, FILETYPE_PHOTOALBUM, $albumId);
		
		if ($album['ownerId'] != $_SESSION['userId']) {
			header('Location: '.$config['start_page']);
			die;
		}
	}

	/* User uploaded a file, process it */
	if (!empty($_FILES['image1']) && isset($_POST['catid'])) {
		$file_uploaded = false;
		
		$fileId = handleFileUpload($db, $_SESSION['userId'], FILETYPE_PHOTOALBUM, $_FILES['image1'], $_POST['catid']);
		if ($fileId !== false) {

			if (!empty($_POST['desc'])) {
				addComment($db, COMMENT_FILE_DESC, $fileId, $_POST['desc']);
			}

			//header('Location: photos_show.php?id='.$fileId);
			header('Location: photoalbums_show.php?id='.$albumId);
			die;
		}
	}

	include('design_head.php');
	include('design_user_head.php');
	
	$content = '';

	$list = getFileCategories($db, FILETYPE_PHOTOALBUM, $_SESSION['userId']);

	if (count($list)) {

		$content =
			'<b>Laste opp et bilde</b><br><br>'.
			'Herfra kan du laste opp bilder til ditt fotoalbum.<br><br>'.
			'Tillatte filtyper er jpeg, gif og png.<br>'.
			'Bilder som er st&oslash;rre enn '.$config['image_max_width'].'x'.$config['image_max_height'].' vil bli forminsket til passe st&oslash;rrelse.<br><br>';
			'<br><br>';

		$content .=
			'<form method="post" name="upload" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?id='.$albumId.'">'.
			'<input type="hidden" name="MAX_FILE_SIZE" value="10000000">'.
			'<input name="image1" type="file"><br><br>'.
			'Lagre bildene i dette albumet:<br>'.
			'<select name="catid">';

		for ($i=0; $i<count($list); $i++) {
			$content .= '<option value="'.$list[$i]['categoryId'].'"';
			if ($list[$i]['categoryId'] == $albumId) $content .= ' selected';
			$content .= '>'.$list[$i]['categoryName'].' ('.$list[$i]['fileCount'];
			if ($list[$i]['fileCount'] == 1) $content .= ' bild)';
			else  $content .= ' bilder)';
		}
			
		$content .=
			'</select><br><br>'.
			'Kommentar til bilde (Frivillig):<br>'.
			'<textarea name="desc" cols=65 rows=6></textarea><br><br>'.
			'<input type="submit" class="button" value="Laste opp"><br>'.
			'</form>';

		if ($albumId) $content .= '<br><br><a href="photoalbums_show.php?id='.$albumId.'">Vise albumet</a>';

	} else {
		
		$content .= 'Du m&aring;ste skapa ett fotoalbum innan du kan ladda upp bilder.<br><br>';
		$content .= '<a href="photoalbums_categories.php">Skapa fotoalbum</a>';
		
	}

	echo '<div id="user_fotoalbum_content">';
	echo MakeBox('<a href="photoalbums.php?id='.$_SESSION['userId'].'">Fotoalbum</a>', $content);
	echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');
	
	if (isset($file_uploaded) && !$file_uploaded) {
		JS_Alert('Problem vid uppladdning. Endast bildfiler &auml;r till&aring;tna');
	}
?>
