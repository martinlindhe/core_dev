<?
	//id = fileId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$fileId = $_GET['id'];
	$data = getFile($db, $fileId);
	if (!$data || $data['fileType'] != FILETYPE_PHOTOALBUM || $data['uploaderId'] == $_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (isset($_POST['reason'])) {
		$queueId = addToModerationQueue($db, $fileId, MODERATION_REPORTED_PHOTO);
		addComment($db, COMMENT_MODERATION_QUEUE, $queueId, $_POST['reason']);

		header('Location: photos_show.php?id='.$fileId);
		die;
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

		$content  = 'Rapporter bilde - '.$data['fileName'].'<br><br>';
		
		if ($resized_image) {
			$content .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$org_width.','.$org_height.')">';
		}
		$content .= '<img src="file.php?id='.$data['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$data['fileName'].'">';
		if ($resized_image) {
			$content .= '</a>';
		}
		$content .= '<br><br>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">';
		$content .= 'Begrunn anmeldelsen:<br>';
		$content .= '<textarea name="reason" cols=64 rows=6></textarea><br><br>';

		$content .= '<input type="submit" class="button" value="'.$config['text']['link_report'].'">';
		$content .= '</form><br><br>';

		$content .= '<a href="photos_show.php?id='.$fileId.'">Tilbake til bildeoversikten</a>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$data['ownerId'].'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>