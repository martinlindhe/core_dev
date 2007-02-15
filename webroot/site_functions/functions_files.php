<?
	$config['allowed_audio_extensions'] = array('.mp3');
	$config['allowed_image_extensions'] = array('.jpg', '.png', '.gif');
	$config['allowed_image_mimetypes'] = array('image/jpeg', 'image/png');

	$config['image_max_width']	= 1280;		//bigger images will be resized to this size
	$config['image_max_height']	= 1024;	
	$config['image_jpeg_quality'] = 70;		//0-100% quality for recompression of very large uploads (like digital camera pictures)
	$config['image_png_compression_level'] = 1; //1-9, 9 being best compression and taking most cpu to achieve (used when calling external bmp2png converter)

	$config['thumbnail_width']	= 285;		//pixel width on thumbnails
	$config['thumbnail_height']	= 285;	
	$config['thumbnail_quality']	= 60;	//0-100% quality of thumbnail pictures, 100% is best quality (for jpeg thumbnails)
	$config['thumbnail_resample'] = true;	//use imagecopyresampled() instead of imagecopyresized() to create better-looking thumbnails. seems to work best

	$config['upload_dir'] = 'c:/webroot_upload/';
	$config['thumbs_dir'] = 'c:/webroot_upload/thumbs/';

	define('FILETYPE_INFOFIELD',			100); /* File is attached to a infofield */
	define('FILETYPE_PR',							101);	/* File is attached to a PR */
	define('FILETYPE_BLOG',						102);	/* File is attached to a blog */
	define('FILETYPE_PHOTOALBUM',			103);	/* File is uploaded to a photoalbum */
	define('FILETYPE_USERDATAFIELD',	104); /* File belongs to a userdata field */
	define('FILETYPE_NORMAL_UPLOAD',	105);	/* File is uploaded by a user */

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





	function getFile(&$db, $fileId)
	{
		if (!is_numeric($fileId)) return false;
		
		$sql  = 'SELECT t1.*,t3.userName AS uploaderName FROM tblFiles AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.uploaderId=t3.userId) ';
		$sql .= 'WHERE t1.fileId='.$fileId;
		return dbOneResult($db, $sql);
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

	/* Returns array with width, height propotionally resized to maximum $to_width and $to_height */
	function resizeImageCalc($filename, $to_width, $to_height)
	{
		global $config;

		if (!is_file($filename)) return false;

		list($orig_width, $orig_height) = getimagesize($filename);
		if (!$orig_width || !$orig_height) return false;

		$max_width = $config['image_max_width'];
		$max_height = $config['image_max_height'];

		if ($to_width && ($to_width < $max_width)) $max_width = $to_width;
		if ($to_height && ($to_height < $max_height)) $max_height = $to_height;

		// Proportionally resize the image to the max sizes specified above
		$x_ratio = $max_width / $orig_width;
		$y_ratio = $max_height / $orig_height;

		if (($orig_width <= $max_width) && ($orig_height <= $max_height))
		{
			$tn_width = $orig_width;
			$tn_height = $orig_height;
		}
		elseif (($x_ratio * $orig_height) < $max_height)
		{
			$tn_height = ceil($x_ratio * $orig_height);
			$tn_width = $max_width;
		}
		else
		{
			$tn_width = ceil($y_ratio * $orig_width);
			$tn_height = $max_height;
		}
		
		return Array($tn_width, $tn_height);
	}

	function resizeImage($filename, $to_width=0, $to_height=0)
	{
		global $config;

		if (empty($to_width) && empty($to_height)) return false;

		$data = getimagesize($filename);
		$orig_width = $data[0];
		$orig_height = $data[1];
		$mime_type = $data['mime'];

		list($tn_width, $tn_height) = resizeImageCalc($filename, $to_width, $to_height);

		//echo 'Resizing from '.$orig_width.'x'.$orig_height.' to '.$tn_width.'x'.$tn_height.'<br>';

		switch ($mime_type)
		{
   		case 'image/png':	$image = imagecreatefrompng($filename); break;
   		case 'image/jpeg': $image = imagecreatefromjpeg($filename); break;
   		case 'image/gif': $image = imagecreatefromgif($filename); break;
   		default: echo 'Unknown mime type '.$mime_type; die;
		}

		$image_p = imagecreatetruecolor($tn_width, $tn_height);
	
		if ($config['thumbnail_resample']) {
			imagecopyresampled($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		} else {
			imagecopyresized($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		}

		imagedestroy($image);

		return $image_p;
	}
	
	function handleFileUpload(&$db, $ownerId, $fileType, $FileData, $categoryId = 0)
	//$ownerId = ägaren till filen, ett fieldId t.ex
	//$FileData = $_FILES['file1']
	{
		global $config;
		if (!is_numeric($ownerId) || !is_numeric($fileType) || !is_numeric($categoryId)) return false;

		if (!is_uploaded_file($FileData['tmp_name'])) {
			logEntry($db, 'Possible file upload attack!');
			return false;
		}

		$dbFileName = dbAddSlashes($db, basename(strip_tags($FileData['name'])));
		$dbFileMime = dbAddSlashes($db, strip_tags($FileData['type']));

		$file_lastname = '';
		$file_firstname = $dbFileName;
		$pos = strrpos($file_firstname, '.');
		if ($pos !== false) {
			$file_lastname = strtolower(substr($file_firstname, $pos));
			$file_firstname = substr($file_firstname, 0, $pos);
		}

		if ($fileType == FILETYPE_PHOTOALBUM || $fileType == FILETYPE_USERDATAFIELD) {

			/* File is not of allowed image type */
			if (!in_array($file_lastname, $config['allowed_image_extensions'])) {
				unlink($FileData['tmp_name']);
				//return 'Otill&aring;ten filtyp';
				return 'Ikke tillat filtype';
			}
		}

		$img_size = getimagesize($FileData['tmp_name']);
		if ($img_size['mime']) $dbFileMime = $img_size['mime'];

		if ($dbFileMime == 'image/bmp')
		{
 			//$image = imagecreatefrombmp($filename);		//this is a extremely slow bmp-handling implementation in php, avoid at all causes
   																								//-- are known to spin up apache.exe to over 180mb ram usage

			/* Always recode bmp's as png's to save space */
			$reencodedFile = $FileData['tmp_name'].'_2pngtmp';
			exec('bmp2png -'.escapeshellarg($config['image_png_compression_level']).' -O '.escapeshellarg($reencodedFile).' '.escapeshellarg($FileData['tmp_name']));
			unlink($FileData['tmp_name']);
			rename($reencodedFile, $FileData['tmp_name']);
			$dbFileName .= '_reencoded.png';
			$img_size['mime'] = 'image/png';
			$dbFileMime = 'image/png';
		}

		$fileSize = filesize($FileData['tmp_name']);

		/* Resize the image if it is too big */
		if (($img_size[0] > $config['image_max_width']) || ($img_size[1] > $config['image_max_height']))
		{
			$resizedImage = resizeImage($FileData['tmp_name'], $config['image_max_width'], $config['image_max_height']);
			
			$resizedFile = $FileData['tmp_name'].'_resizetmp';

			switch ($img_size['mime'])
			{
	   		case 'image/png':
	   			imagepng($resizedImage, $resizedFile);
					$dbFileMime = 'image/png';
					$dbFileName = $file_firstname.'_shrinked.png';
	   			break;

  	 		case 'image/gif':
  	 			imagegif($resizedImage, $resizedFile);
					$dbFileMime = 'image/gif';
					$dbFileName = $file_firstname.'_shrinked.gif';
  	 			break;
  	 			
   			case 'image/jpeg':
   				imagejpeg($resizedImage, $resizedFile, $config['image_jpeg_quality']);
					$dbFileMime = 'image/jpeg';
					$dbFileName = $file_firstname.'_shrinked.jpg';
   				break;

   			default: echo 'Unknown mime type '.$img_size['mime']; die;
			}

			$fileSize = filesize($resizedFile);
			unlink($FileData['tmp_name']);
			rename($resizedFile, $FileData['tmp_name']);
		}

  	$sql = 'INSERT INTO tblFiles SET fileName="'.$dbFileName.'",fileSize='.$fileSize.',fileMime="'.$dbFileMime.'",ownerId='.$ownerId.',uploaderId='.$_SESSION['userId'].',uploaderIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).',timeCreated=NOW(),fileType='.$fileType.',categoryId='.$categoryId;
  	dbQuery($db, $sql);
  	$fileId = $db['insert_id'];

		$uploadfile = $config['upload_dir'].$fileId;

		if (!move_uploaded_file($FileData['tmp_name'], $uploadfile)) {
			logEntry($db, 'Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile);
		}
		return $fileId;
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