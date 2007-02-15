<?
	//id = fileId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$fileId = $_GET['id'];
	$data = getFile($db, $fileId);
	if (!$data || $data['fileType'] != FILETYPE_PHOTOALBUM || $data['uploaderId'] != $_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['desc'])) {
		$comment = getLastComment($db, COMMENT_FILE_DESC, $fileId);
		if ($comment) {
			updateComment($db, COMMENT_FILE_DESC, $fileId, $comment['commentId'], $_POST['desc']);
		} else {		
			addComment($db, COMMENT_FILE_DESC, $fileId, $_POST['desc']);
		}
	}
	

	include('design_head.php');
	include('design_user_head.php');


		$content = '<div style="float:right; width:150px;">';
			$c2  = '<a href="photos_show.php?id='.$fileId.'">Bildeoversikt</a><br><br>';
			$c2 .= '<a href="photos_move.php?id='.$fileId.'">Flytte bildet</a><br><br>';
			$c2 .= '<a href="photos_delete.php?id='.$fileId.'">Slette bildet</a>';
			$content .= MakeBox('|Valg', $c2);
		$content .= '</div>';



		$file_name = $file_name = $config['upload_dir'].$data['fileId'];
		list($t_width, $t_height) = resizeImageCalc($file_name, $config['thumbnail_width'], $config['thumbnail_height']);
		list($org_width, $org_height) = getimagesize($file_name);
		
		$resized_image = false;
		if ($org_width > $t_width || $org_height > $t_height) {
			$resized_image = true;
		}

		$content .= 'Redigere bildekommentaren - '.$data['fileName'].'<br><br>';
		
		if ($resized_image) {
			$content .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$org_width.','.$org_height.')">';
		}
		$content .= '<img src="file.php?id='.$data['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$data['fileName'].'">';
		if ($resized_image) {
			$content .= '</a>';
		}
		$content .= '<br><br>';

		$comment = getLastComment($db, COMMENT_FILE_DESC, $fileId);
		$content .= 'Bildekommentar';
		if ($comment) $content .= ', skrevet '.strtolower(getRelativeTimeLong($comment['commentTime']));
		$content .= ':<br>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">';
		$content .= '<textarea name="desc" cols=65 rows=8>';
		if ($comment) {
			$content .= $comment['commentText'];
		}
		$content .= '</textarea><br><br>';
		$content .= '<input type="submit" class="button" value="Lagre kommentar">';
		$content .= '</form><br>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$data['ownerId'].'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>