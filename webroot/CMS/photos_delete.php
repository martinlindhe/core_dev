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
	
		if (isset($_GET['confirmed'])) {
			deleteFile($db, $fileId);
			deleteComments($db, COMMENT_FILE_DESC, $fileId);
			
			$content  = 'Bilden &auml;r raderad!<br><br>';
			//$content .= '<a href="photoalbums.php?id='.$_SESSION['userId'].'">Tillbaka till mina fotoalbum</a>';
			$content .= '<a href="photoalbums_show.php?id='.$data['categoryId'].'">Tillbaka till fotoalbumet '.$data['categoryName'].'</a>';
		} else {	

			$file_name = $file_name = $config['upload_dir'].$data['fileId'];
			list($t_width, $t_height) = resizeImageCalc($file_name, $config['thumbnail_width'], $config['thumbnail_height']);
			list($org_width, $org_height) = getimagesize($file_name);
			
			$resized_image = false;
			if ($org_width > $t_width || $org_height > $t_height) {
				$resized_image = true;
			}
	
			$content  = 'Slette bildet - '.$data['fileName'].'<br><br>';
			
			if ($resized_image) {
				$content .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$org_width.','.$org_height.')">';
			}
			$content .= '<img src="file.php?id='.$data['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$data['fileName'].'">';
			if ($resized_image) {
				$content .= '</a>';
			}
			$content .= '<br><br>';
			
			$content .= 'Er du sikker p&aring; at du vil slette dette bildet?<br><br>';
			
			$content .=
				'<table width="100%" cellpadding=0 cellspacing=0>'.
				'<tr align="center">'.
					'<td width="50%"><a href="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'&confirmed">'.$config['text']['prompt_yes'].'</a></td>'.
					'<td><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>'.
				'</tr>'.
				'</table>';
		}
		
		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$data['ownerId'].'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>