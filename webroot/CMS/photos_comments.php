<?
	//id = fileId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$fileId = $_GET['id'];
	$data = getFile($db, $fileId);
	if (!$data || $data['fileType'] != FILETYPE_PHOTOALBUM) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (!empty($_POST['comment'])) {
		addComment($db, COMMENT_PHOTO, $fileId, $_POST['comment']);
	}

	include('design_head.php');
	include('design_user_head.php');

		$file_name = $file_name = $config['upload_dir'].$data['fileId'];
		list($t_width, $t_height) = resizeImageCalc($file_name, $config['thumbnail_width'], $config['thumbnail_height']);
		list($org_width, $org_height) = getimagesize($file_name);
		
		$resized_image = false;
		if ($org_width > $t_width || $org_height > $t_height) {
			$resized_image = true;
		}

		$content = 'Kommentere til '.$data['fileName'].'<br>';
		if ($data['categoryName']) {
			$content .= '(Tilh&oslash;rer albumet <a href="photoalbums_show.php?id='.$data['categoryId'].'">'.$data['categoryName'].'</a>)<br><br>';
		}
		$content .= 'Laste opp av '.nameLink($data['uploaderId'], $data['uploaderName']).' '.strtolower(getRelativeTimeLong($data['uploadTime'])).'<br>';
		$content .= '<br>';
		$content .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$org_width.','.$org_height.')">';
		$content .= '<img src="file.php?id='.$data['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$data['fileName'].'">';
		$content .= '</a>';
		$content .= '<br><br>';
		$content .= '<a href="photos_show.php?id='.$fileId.'">Tilbake til bildeoversikt</a><br><br>';

		$list = getComments($db, COMMENT_PHOTO, $fileId);
		for ($i=0; $i<count($list); $i++) {
			$content .= '<div class="user_photo_comments">';
			$content .= getRelativeTimeLong($list[$i]['commentTime']).' fra '.nameLink($list[$i]['userId'], $list[$i]['userName']).':<br>';
			$content .= $list[$i]['commentText'];
			$content .= '</div><br>';
		}

		$content .=
			'<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">'.
			'<textarea name="comment" cols=63 rows=4></textarea><br><br>'.
			'<input type="submit" class="button" value="Lagre kommentar">'.
			'</form>';
		

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$data['ownerId'].'">Fotoalbum</a>|Kommentere', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>