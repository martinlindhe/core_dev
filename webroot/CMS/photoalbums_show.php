<?
	//id = albumId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$albumId = $_GET['id'];
	$album = getFileCategory($db, FILETYPE_PHOTOALBUM, $albumId);
	if (!$album) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

		$content = '';

		$list = getFilesByCategory($db, FILETYPE_PHOTOALBUM, $album['ownerId'], $albumId);

		if (count($list)) {

			$tot_size = 0;
			$content .= '<b>'.$album['categoryName'].'</b><br>';
			$content .= 'Album lagd av '.nameLink($album['ownerId'], $album['ownerName']).' den '.strtolower(formatShortDate($album['timeCreated'])).'<br><br>';
			$content .= '<div>';

			for ($i=0; $i<count($list); $i++) {
				$file_name = $config['upload_dir'].$list[$i]['fileId'];
				//if (!file_exists($file_name)) continue;

				list($t_width, $t_height) = resizeImageCalc($file_name, $config['thumbnail_width'], $config['thumbnail_height']);

				$content .=
						'<div class="user_photoalbum_thumb">'.
						'<a href="photos_show.php?id='.$list[$i]['fileId'].'">'.
						'<img src="file.php?id='.$list[$i]['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$list[$i]['fileName'].'">'.
						'</a>'.
						'</div>';
						
				$tot_size += $list[$i]['fileSize'];
			}

			//shows 3 image "containers" per row, fill out missing images
			if (count($list)%3) {
				for ($i=0; $i< (3-count($list)%3); $i++) {
					$content .=
						'<div class="user_photoalbum_thumb">'.
						'</div>';
				}
			}
			
			$content .= 'Totalt '.formatDataSize($tot_size).' i '.count($list).' filer.';
			$content .= '</div>';
		} else {
			$content .= 'Albumet <b>'.$album['categoryName'].'</b>, laget av '.nameLink($album['ownerId'], $album['ownerName']).' inneholder ingen bilder.<br>';
		}
		
		$content .= '<br>';

		if ($album['ownerId'] == $_SESSION['userId']) {
			$content .= '<a href="photoalbums_upload.php?id='.$albumId.'">Last opp bilder til dette albumet</a><br><br>';
			$content .= '<a href="photoalbums_edit.php?id='.$albumId.'">Rediger dette albumet</a><br><br>';
			$content .= '<a href="photoalbums_delete.php?id='.$albumId.'">Slett dette albumet</a><br><br>';
			$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';
		}

		$title = $album['categoryName'];
		if (mb_strlen($title) > 28) {
			$title = mb_substr($title, 0, 28).'...';
		}
		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$album['ownerId'].'">Fotoalbum</a>|'.$title, $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>
