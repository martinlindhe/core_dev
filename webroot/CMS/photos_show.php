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
	
	updateFileViews($db, $fileId);

	include('design_head.php');
	include('design_user_head.php');

		$file_name = $file_name = $config['upload_dir'].$data['fileId'];
		list($t_width, $t_height) = resizeImageCalc($file_name, 400, 300);
		list($org_width, $org_height) = getimagesize($file_name);
		
		$resized_image = false;
		if ($org_width > $t_width || $org_height > $t_height) {
			$resized_image = true;
		}

		$content  = 'Bildeoversikt - '.$data['fileName'];
		$content .= ' <a href="file.php?id='.$data['fileId'].'&dl"><img src="icons/download.gif" height=11 width=11 title="Download" align="absmiddle"></a>';
		if ($data['categoryName']) {
			$content .= ' (i <a href="photoalbums_show.php?id='.$data['categoryId'].'">'.$data['categoryName'].'</a>)';
		}
		$content .= '<br><br>';

		if ($resized_image) {
			$content .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$org_width.','.$org_height.')">';
		}
		$content .= '<img src="file.php?id='.$data['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$data['fileName'].'">';
		if ($resized_image) {
			$content .= '</a>';
		}
		$content .= '<br>';

		if ($resized_image) {
			//$content .= '<i>Detta &auml;r en f&ouml;rminskad version av orginalbilden.<br>Klicka p&aring; bilden f&ouml;r att se orginalet.</i><br><br>';
			$content .= '<i>Dette er en forminsket versjon av bildet. Klikk p&aring; bildet for &aring; se originalen.</i><br><br>';
		} else {
			$content .= '<i>Bildene vises i sin originale st&oslash;rrelse.</i><br><br>';
		}

		$comment = getLastComment($db, COMMENT_FILE_DESC, $fileId);
		if ($comment) {
			$content .= 'Komentar: <b>'.$comment['commentText'].'</b><br><br>';
		}
		
		
		$content .= 'Filst&oslash;rrelse: '.formatDataSize($data['fileSize']).'<br>';
		$content .= 'Originaldimensjon: '.$org_width.'x'.$org_height.'<br>';
		$content .= 'Antall visninger: '.$data['cnt'].'<br>';
		$content .= 'Lastet opp av '.nameLink($data['uploaderId'], $data['uploaderName']).' '.getRelativeTimeLong($data['uploadTime']).'<br>';
		$content .= '<br>';

		
		$cnt = getCommentsCount($db, COMMENT_PHOTO, $fileId);
		$content .= '<a href="photos_comments.php?id='.$fileId.'">'.$cnt;
		if ($cnt == 1) $content .= ' kommentar</a><br><br>';
		else $content .= ' kommentere</a><br><br>';

		if ($data['uploaderId'] == $_SESSION['userId']) {
			$content .= '<a href="photos_edit.php?id='.$fileId.'">'.$config['text']['link_edit'].' bildet</a><br><br>';
		} else {
			$content .= '<a href="photos_report.php?id='.$fileId.'">Rapporter bildet</a><br>';
		}

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$data['ownerId'].'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>