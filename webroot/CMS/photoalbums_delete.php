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
	
	if (isset($_GET['confirmed'])) {
		deleteFileCategory($db, FILETYPE_PHOTOALBUM, $albumId);
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

		$content =
			'<b>Slette fotoalbumet</b><br><br>'.
			' Vil du virkelig slette albumet <b>'.$album['categoryName'].'</b>?<br><br>';
			
		$cnt = getFilesByCategoryCount($db, FILETYPE_PHOTOALBUM, $_SESSION['userId'], $albumId);
		
		$content .= 'Albumet inneholder '.$cnt.' bilder, bilder, disse vil bli slettet.<br><br>';

		$content .=
			'<table width="100%" cellpadding=0 cellspacing=0 border=0><tr>'.
			'<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$albumId.'&confirmed">'.$config['text']['prompt_yes'].'</a></td>'.
			'<td align="center"><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>'.
			'</tr></table>';


		$content .= '<br><br><a href="photoalbums_show.php?id='.$albumId.'">Vise albumet</a>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$album['ownerId'].'">Fotoalbum</a>|Radera', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');
?>