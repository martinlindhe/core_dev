<?
	define('FILETYPE_INFOFIELD',			100); /* File is attached to a infofield */
	define('FILETYPE_PR',							101);	/* File is attached to a PR */
	define('FILETYPE_BLOG',						102);	/* File is attached to a blog */
	define('FILETYPE_PHOTOALBUM',			103);	/* File is uploaded to a photoalbum */
	define('FILETYPE_USERDATAFIELD',	104); /* File belongs to a userdata field */
	define('FILETYPE_NORMAL_UPLOAD',	105);	/* File is uploaded by a user */






/*
handle uploaded images:


*/









	//todo: all code below this line are not ported yet:
	
	$config['thumbnail_width']	= 285;		//pixel width on thumbnails
	$config['thumbnail_height']	= 285;	
	$config['thumbnail_quality']	= 60;	//0-100% quality of thumbnail pictures, 100% is best quality (for jpeg thumbnails)

	$config['upload_dir'] = 'c:/webroot_upload/';
	$config['thumbs_dir'] = 'c:/webroot_upload/thumbs/';

	function sendTextFile($realFileName, $sendFileName)
	{
		//required for IE6:
		header('Cache-Control: cache, must-revalidate');

		//header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($realFileName)) . ' GMT');
		header('Content-Length: '.filesize($realFileName));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'.$sendFileName.'"');

		readfile($realFileName, 'r');
	}

	function getFilesByCategory(&$db, $fileType, $categoryId)
	{
		if (!is_numeric($fileType) || !is_numeric($categoryId)) return false;

		$sql = 'SELECT * FROM tblFiles WHERE fileType='.$fileType.' AND categoryId='.$categoryId;
		$sql .= ' ORDER BY timeCreated DESC ';

		return dbArray($db, $sql);
	}

	//used by functions_infofields.php
	function getFiles(&$db, $fieldId, $fileType=0)
	{
		if (!is_numeric($fieldId) || !is_numeric($fileType)) return array();

		$sql = 'SELECT t1.*,t2.userName AS uploaderName FROM tblFiles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.uploaderId=t2.userId) ';
		$sql .= 'WHERE t1.ownerId='.$fieldId;
		if ($fileType) $sql .= ' AND t1.fileType='.$fileType;
		$sql .= ' ORDER BY t1.timeCreated ASC';

		return dbArray($db, $sql);
	}



	function getFileName(&$db, $fileId)
	{
		if (!is_numeric($fileId)) return false;
		
		$sql = 'SELECT fileName FROM tblFiles WHERE fileId='.$fileId;
		return dbOneResultItem($db, $sql);
	}
	
	function deleteFile(&$db, $fileId)
	{
		global $config;

		if (!is_numeric($fileId)) return false;

		$data = getFile($db, $fileId);
		if ($data['uploaderId'] != $_SESSION['userId']) {
			return false;
		}

		$sql = 'DELETE FROM tblFiles WHERE fileId='.$fileId;
		dbQuery($db, $sql);

		$filename = $config['upload_dir'].$fileId;
		unlink($filename);
	}


	function updateFileViews(&$db, $fileId)
	{
		if (!is_numeric($fileId) || empty($fileId)) return false;
		if (isset($_SESSION['viewed_files'][$fileId])) return false;		//count 1 view per session

		$sql = 'UPDATE tblFiles SET cnt=cnt+1 WHERE fileId='.$fileId;
		dbQuery($db, $sql);
		
		$_SESSION['viewed_files'][$fileId] = true;
	}
	
	function showFileAttachments(&$db, $ownerId, $file_type, $extra = '')
	{
		global $config;

		if (!is_numeric($ownerId) || !is_numeric($file_type)) return '';

		$info = '';

		switch ($file_type)
		{
			/* When FILETYPE_INFOFIELD, $extra = name of infofield */
			case FILETYPE_INFOFIELD:
				$fieldName = $extra;
				$post_url = AddToURL('infofiles', $fieldName, $_SERVER['PHP_SELF']);
				$description = 'Files attached to this infofield';
				break;
				
			case FILETYPE_PR:
				$fieldName = '';
				$description = 'Files attached to this PR';
				$post_url = AddToURL();
				break;

			case FILETYPE_BLOG:
				$fieldName = '';
				$description = 'Files attached to this blog post';
				$post_url = AddToURL();
				break;
		}
	
		/* User uploaded a file, process it */
		if (!empty($_FILES['file1'])) {
			handleFileUpload($db, $ownerId, $file_type, $_FILES['file1']);
			unset($_FILES['file1']);
		}
				
		/* List attached files */
		$list = getFiles($db, $ownerId, $file_type);

		$info .= '<div class="attached_files">';
		$info .= $description.'<br><br>';
		$info .= '<table width="100%" cellpadding=5 cellspacing=0 border=0>';
		$info .= '<tr><td><b>File details</b></td><td width=90><b>Tag</b></td></tr>';
		$tot_bytes = 0;
		for ($i=0; $i<count($list); $i++) {
			$bgcolor = '#FEFEFE';
			if ($i%2) $bgcolor='#F0F0F0';
			$info .= '<tr><td colspan=2>'.formatFileAttachment($db, $list[$i], $bgcolor, true, $fieldName).'</td></tr>';
			$tot_bytes += $list[$i]['fileSize'];
		}
		$info .= '<tr><td colspan=2><b>';
		if (count($list) == 1) $info .= 'En fil';
		else $info .= count($list).' filer';
		$info .= ' (totalt '.formatDataSize($tot_bytes).')</b></td></tr>';
		$info .= '</table><br>';

		if ($_SESSION['loggedIn']) {
			$info .=
				'<form method="post" enctype="multipart/form-data" action="'.$post_url.'">'.
				'<table width="100%" cellpadding=0 cellspacing=0 border=0>'.
					'<input type="hidden" name="MAX_FILE_SIZE" value="10000000">'.
					'<tr><td><input name="file1" type="file"><br><img src="c.gif" width=1 height=5></td></tr>'.
					'<tr><td><input type="submit" class="button" value="Ladda upp"></td></tr>'.
				'</table>'.
				'</form>';
			}
			
		$info .= '</div>';

		return $info;
	}

	function formatFileAttachment(&$db, $data, $bgcolor = '#F0F0F0', $showExtraInfo = true, $fieldName = '')
	{
		global $config;

		if (!empty($_GET['deletefileattachment']) && is_numeric($_GET['deletefileattachment']))
		{
			if (!isset($_GET['confirmed'])) {
				$deletename = getFileName($db, $_GET['deletefileattachment']);

				$yes_url = AddToURL('infofiles', $fieldName, $_SERVER['PHP_SELF']);
				$yes_url = AddToURL('deletefileattachment', $_GET['deletefileattachment'], $yes_url);
				$yes_url = AddToURL('confirmed', '', $yes_url);

				$info  = 'Are you sure you wish to do delete the file '.$deletename.' (id '.$_GET['deletefileattachment'].') ?<br><br>';
				$info .= '<a href="'.$yes_url.'">Yes, I am sure</a><br><br>';
				$info .= '<a href="'.AddToURL('infofiles', $fieldName, $_SERVER['PHP_SELF']).'">No, wrong button</a>';

				echo $info;
				include('design_foot.php');
				die;

			} else {
				//todo: när det finns view-history till filen, så ska historyn tas bort nu åxå

				/* delete file attachment */
				$sql = 'DELETE FROM tblFiles WHERE fileId='.$_GET['deletefileattachment'];
				dbQuery($db, $sql);

				$file_name = $config['upload_dir'].$_GET['deletefileattachment'];
				if (file_exists($file_name)) unlink($file_name);

				//thumbnails arent always created
				$thumb_name = $config['thumbs_dir'].$_GET['deletefileattachment'].'_'.$config['thumbnail_width'].'w';
				if (file_exists($thumb_name)) unlink($thumb_name);
				unset($_GET['deletefileattachment']);
			}
		}

		$last_name = '';
		$pos = strrpos($data['fileName'], '.');
		if ($pos !== false) $last_name = strtolower(substr($data['fileName'], $pos));
		
		$file_name = $config['upload_dir'].$data['fileId'];
		if (file_exists($file_name)) {
			$img_size = getimagesize($file_name);
			$sha1 = sha1_file($file_name);
		} else {
			$img_size[0] = '?';
			$img_size[1] = '?';
			$sha1 = 'file not found';
		}

		if (!$showExtraInfo && in_array($last_name, $config['allowed_image_extensions'])) {
			$html  =	'<table width='.($config['thumbnail_width']+80).' cellpadding=0 cellspacing=0 border=1><tr>'.
									'<td width='.$config['thumbnail_width'].'>'.
										'<a href="javascript:wnd_imgview('.$data['fileId'].','.$img_size[0].','.$img_size[1].')">'.
										'<img src="file.php?id='.$data['fileId'].'&width='.$config['thumbnail_width'].'" width='.$config['thumbnail_width'].' title="['.$data['fileId'].']"></a>'.
									'</td>'.
									'<td width=80 align="center" valign="top">'.
										formatDataSize($data['fileSize']).'<br>'.
										$data['cnt'].' views<br>'.
										$img_size[0].' x '.$img_size[1].
									'</td>'.
								'</tr></table>';

		} else {
			$html  =
				'<table width="100%" cellpadding=0 cellspacing=0 border=0>'.
					'<tr bgcolor='.$bgcolor.'><td>'.
			
							'<table width="100%" cellpadding=0 cellspacing=0 border=0>'.
								'<tr><td colspan=5>';

								$html .= '<a href="file.php?id='.$data['fileId'].'" target="_blank">'.$data['fileName'].'</a>';
								$fileTag = '[file'.$data['fileId'].']';

								if (in_array($last_name, $config['allowed_image_extensions'])) {
									$html .= ' (<a href="javascript:wnd_imgview_all('.$data['fileId'].','.$img_size[0].','.$img_size[1].')">popup</a>)';
									$fileTag = '[image'.$data['fileId'].']';
								}

								if (in_array($last_name, $config['allowed_audio_extensions'])) {
									$html .= ' (<a href="javascript:wnd_audplay('.$data['fileId'].')">popup</a>)';
								}

			$html .=		' (<a href="file.php?id='.$data['fileId'].'&dl" target="_blank">download</a>)'.
								'</td></tr>'.
								'<tr><td>'.
									'<font size=1>';
									$html .= formatDataSize($data['fileSize']).' (Type: '.$data['fileMime'].')';
									if (in_array($last_name, $config['allowed_image_extensions'])) {
										$html .= ' '.$img_size[0].' x '.$img_size[1];
									}
									if ($_SESSION['isAdmin']) {
										$html .= '<br>Uploaded at '.getDateStringShort($data['timeCreated']);
										$html .= ' by '.nameLink($data['uploaderId'], $data['uploaderName']);
										$html .= ', '.$data['cnt'].' views<br>';
										$html .= 'SHA1: '. $sha1;
									}
			$html .=		'</font>'.
								'</td>'.
								'<td width=16>';
									if ($showExtraInfo) {

										$tmp_url = AddToURL('infofiles', $fieldName, $_SERVER['PHP_SELF']);
										$tmp_url = AddToURL('deletefileattachment', $data['fileId'], $tmp_url);

										$html .= '<a href="'.$tmp_url.'"><img src="icons/delete.png" width=16 height=16 title="Delete file"></a>';
									} else {
										$html .= '&nbsp;';
									}
			$html .=	'</td>'.
								'<td width=10><img src="c.gif" width=1 height=1></td>'.
								'<td width=32>';
									if (in_array($last_name, $config['allowed_image_extensions'])) {
										$html .= '<a href="javascript:wnd_imgview('.$data['fileId'].','.$img_size[0].','.$img_size[1].')">';
										$html .= '<img src="file.php?id='.$data['fileId'].'&width=32" width=32 border=0 title="'.$data['fileName'].'">';
										$html .= '</a>';
									}
									switch ($last_name) {
										case '.pdf': $html .= '<img src="file_icons/pdf_32.png" width=32 height=32 title="Acrobat PDF document">'; break;
										case '.mp3': $html .= '<img src="file_icons/mp3_32.png" width=32 height=32 title="MP3 Audio file">'; break;
										default: $html .= '&nbsp;';
									}
			$html .=	'</td></tr>'.
							'</table>'.
						'</td>';
						if ($showExtraInfo && $_SESSION['isAdmin']) $html .= '<td width=10>&nbsp;</td><td width=90><b>'.$fileTag.'</b></td>';

			$html .=
					'</tr></table>';
		}
		
		return $html;
	}
?>