<?
/*
	Files class - Handle file upload, image manipulating, file management

	Uses tblFiles

	Written by Martin Lindhe, 2007
	
	Uses php_id3.dll if enabled, to show more details of mp3s in the file module

	//todo: rename tblFiles.timeUploaded to tblFiles.timeCreated
*/

require_once('atom_comments.php');			//for image comments support
require_once('atom_categories.php');		//for file categories support

define('FILETYPE_WIKI',						1); // The file is a wiki attachment
define('FILETYPE_BLOG',						2);	// The file is a blog attachment
define('FILETYPE_NEWS',						3);	// The file is a news attachment

define('FILETYPE_FILEAREA_UPLOAD',4);	/* File is uploaded to a file area */
define('FILETYPE_USERFILE',				5);	/* File is uploaded to the user's own file area */
define('FILETYPE_USERDATA',				6);	/* File is uploaded to a userdata field */
define('FILETYPE_FORUM',					7);	/* File is attached to a forum post */
define('FILETYPE_PROCESS',				8);	/* File uploaded to be processed */
define('FILETYPE_PROCESS_CLONE',	9);	/* a clone entry for a process file. */

class Files
{
	/* Non configurable, shouldnt be needed to be changed */
	private $htaccess = "Deny from all\nOptions All -Indexes";
	private $resample_resized			= true;	//use imagecopyresampled() instead of imagecopyresized() to create better-looking thumbnails

	public $image_mime_types = array(
		'image/jpeg',
		'image/png',
		'image/gif');

	public $audio_mime_types	= array(
		'audio/mpeg', 'audio/x-mpeg');	//.mp3 file

	public $video_mime_types = array(
		'video/mpeg',			//.mpg file
		'video/avi',			//.avi file
		'video/x-ms-wmv',	//Microsoft .wmv file
		'video/3gpp');		//.3gp video file

	public $document_mime_types = array(
		'text/plain',					//normal text file
		'application/msword',	//Microsoft .doc file
		'application/pdf');		//Adobe .pdf file

	/* User configurable settings */
	public $upload_dir = 'e:/devel/webupload/default';						//	'/tmp/';
	public $thumbs_dir = 'e:/devel/webupload/default/thumbs/';		//	'/tmp/';

	private $image_max_width			= 1100;	//bigger images will be resized to this size
	private $image_max_height			= 900;
	public $thumb_default_width		= 80;
	public $thumb_default_height	= 80;
	private $image_jpeg_quality		= 70;		//0-100% quality for recompression of very large uploads (like digital camera pictures)

	private $count_file_views			= false;	//auto increments the "cnt" in tblFiles in each $files->sendFile() call
	public $anon_uploads					= false;	//allow unregisterd users to upload files
	private $apc_uploads					= false;		//enable support for php_apc + php_uploadprogress calls

	/* If $image_convert are enabled, it uses ImageMagick to convert the following image formats:
		- BMP images gets converted to JPG
		- SVG images gets converted to PNG

		Used regularry with ImageMagick-6.3.4-4-Q16-windows-static.exe

		 ImageMagick is a open source and multi platform image converter
		 http://www.imagemagick.org/download/
	*/
	private $image_convert				= true;

	function __construct(array $config)
	{
		global $session;

		if (isset($config['upload_dir'])) $this->upload_dir = $config['upload_dir'];
		if (isset($config['thumbs_dir'])) $this->thumbs_dir = $config['thumbs_dir'];

		if (isset($config['image_max_width'])) $this->image_max_width = $config['image_max_width'];
		if (isset($config['image_max_height'])) $this->image_max_height = $config['image_max_height'];
		if (isset($config['thumb_default_width'])) $this->thumb_default_width = $config['thumb_default_width'];
		if (isset($config['thumb_default_height'])) $this->thumb_default_height = $config['thumb_default_height'];
		if (isset($config['image_jpeg_quality'])) $this->image_jpeg_quality = $config['image_jpeg_quality'];

		if (isset($config['count_file_views'])) $this->count_file_views = $config['count_file_views'];
		if (isset($config['anon_uploads'])) $this->anon_uploads = $config['anon_uploads'];
		if (isset($config['apc_uploads'])) $this->apc_uploads = $config['apc_uploads'];
		if (isset($config['image_convert'])) $this->image_convert = $config['image_convert'];
	}

	//Visar alla filer som är uppladdade i en publik "filarea" (FILETYPE_FILEAREA_UPLOAD)
	//Eller alla filer som tillhör en wiki (FILETYPE_WIKI)
	function showFiles($fileType, $ownerId = 0, $categoryId = 0)
	{
		global $session, $db, $config;
		if (!is_numeric($fileType) || !is_numeric($categoryId)) return;

		if ($fileType == FILETYPE_FILEAREA_UPLOAD || $fileType == FILETYPE_USERFILE || $fileType == FILETYPE_WIKI) {
			if (!empty($_GET['file_category_id']) && is_numeric($_GET['file_category_id'])) $categoryId = $_GET['file_category_id'];
		}

		if (($session->id || $this->anon_uploads) && !empty($_FILES['file1'])) {
			$this->handleUpload($_FILES['file1'], $fileType, $ownerId, $categoryId);
			unset($_FILES['file1']);	//to avoid further processing of this file upload elsewhere
			if ($fileType == FILETYPE_WIKI) {
				addRevision(REVISIONS_WIKI, $ownerId, 'File uploaded...', now(), $session->id, REV_CAT_FILE_UPLOADED);
			}
		}

		$userid = $session->id;
		$username = $session->username;

		$action = '';
		if ($categoryId) $action = '?file_category_id='.$categoryId;

		echo '<div id="ajax_anim" style="display:none; float:right; background-color: #eee; padding: 5px; border: 1px solid #aaa;">';
		echo '<img id="ajax_anim_pic" alt="AJAX Loading ..." title="AJAX Loading ..." src="'.$config['core_web_root'].'gfx/ajax_loading.gif"/></div>';

		echo '<div class="file_gadget">';

		echo '<div class="file_gadget_header">';

		switch ($fileType)
		{
			case FILETYPE_USERFILE:
				if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
					$userid = $_GET['id'];
					$username = getUserName($userid);
				}
				echo 'Files:'.$username;
				echo getCategoriesSelect(CATEGORY_USERFILE, 0, '', 0, URLadd('file_category_id')).'<br/>';
				break;

			case FILETYPE_FILEAREA_UPLOAD:
				if (!$categoryId) echo 'File area - Root Level content';
				else echo ' - '.getCategoryName(CATEGORY_USERFILE, $categoryId).' content';
				break;

			case FILETYPE_WIKI:
				if (!$categoryId) echo 'Wiki files - Root Level content';
				echo 'Wiki attachments';
				echo getCategoriesSelect(CATEGORY_WIKIFILE, 0, '', 0, URLadd('file_category_id')).'<br/>';
				break;

			case FILETYPE_BLOG:
				echo 'Blog attachments';
				break;

			case FILETYPE_NEWS:
				echo 'News article attachments';
				break;

			default:
				die('unknown filetype');
		}
		echo '</div>';

		$q = 'SELECT * FROM tblFiles WHERE categoryId='.$categoryId.' AND fileType='.$fileType.' AND ownerId='.$ownerId.' ORDER BY timeUploaded ASC';
		$list = $db->getArray($q);

		$this->showImageGadgetXHTML();
		$this->showAudioGadgetXHTML();
		$this->showVideoGadgetXHTML();
		$this->showDocumentGadgetXHTML();

		echo '<div id="zoom_fileinfo" style="display:none"></div>';
		echo '<div class="file_gadget_content">';

		foreach ($list as $row)
		{
			$title = htmlspecialchars($row['fileName']).' ('.formatDataSize($row['fileSize']).')';
			if (in_array($row['fileMime'], $this->image_mime_types)) {
				echo '<div class="file_gadget_entry" id="file_'.$row['fileId'].'" title="'.$title.'" onclick="zoomImage('.$row['fileId'].');"><center>';
				echo makeThumbLink($row['fileId']);
				echo '</center></div>';
			} else if (in_array($row['fileMime'], $this->audio_mime_types)) {
				echo '<div class="file_gadget_entry" id="file_'.$row['fileId'].'" title="'.$title.'" onclick="zoomAudio('.$row['fileId'].',\''.urlencode($row['fileName']).'\');"><center>';
				echo '<img src="'.$config['core_web_root'].'gfx/icon_file_audio.png" width="70" height="70" alt="Audio file"/>';
				echo '</center></div>';
			} else if (in_array($row['fileMime'], $this->video_mime_types)) {
				echo '<div class="file_gadget_entry" id="file_'.$row['fileId'].'" title="'.$title.'" onclick="zoomVideo('.$row['fileId'].',\''.urlencode($row['fileName']).'\');"><center>';
				echo '<img src="'.$config['core_web_root'].'gfx/icon_file_video.png" width="32" height="32" alt="Video file"/>';
				echo '</center></div>';
			} else if (in_array($row['fileMime'], $this->document_mime_types)) {
				echo '<div class="file_gadget_entry" id="file_'.$row['fileId'].'" title="'.$title.'" onclick="zoomFile('.$row['fileId'].');"><center>';
				echo '<img src="'.$config['core_web_root'].'gfx/icon_file_document.png" width="40" height="49" alt="Document"/>';
				echo '</center></div>';
			} else {
				echo '<div class="file_gadget_entry" id="file_'.$row['fileId'].'" title="'.$title.'" onclick="zoomFile('.$row['fileId'].');"><center>';
				echo 'General file:<br/>';
				echo $row['fileMime'].'<br/>';
				echo '</center></div>';
			}
		}
		echo '</div>';

		//fixme: gör ett progress id av session id + random id, så en user kan ha flera paralella uploads
		//fixme: stöd anon_uploads ! den ignoreras totalt idag, dvs anon uploads tillåts aldrig
		if ( 
				($fileType == FILETYPE_USERFILE && $session->id == $userid) ||
				($fileType == FILETYPE_NEWS && $session->isAdmin) ||
				($fileType == FILETYPE_WIKI) ||
				($fileType == FILETYPE_BLOG) ||
				($fileType == FILETYPE_FILEAREA_UPLOAD)
				)
		{
			$file_upload = true;
			if ($fileType == FILETYPE_BLOG) {
				$data = getBlog($ownerId);
				if ($data['userId'] != $session->id) $file_upload = false;
			}

			if ($file_upload) {
				echo '<div id="file_gadget_upload">';
				if (($fileType == FILETYPE_USERFILE && !$categoryId && $session->id == $userid) ||
						($fileType == FILETYPE_WIKI)
						)
				{
					echo '<input type="button" class="button" value="New file category" onclick="show_element_by_name(\'file_gadget_category\'); hide_element_by_name(\'file_gadget_upload\');"/><br/>';
				}

				if ($this->apc_uploads) {
					echo '<form name="ajax_file_upload" method="post" action="'.$action.'" enctype="multipart/form-data" onsubmit="return submit_apc_upload('.$session->id.');">';
					echo '<input type="hidden" name="APC_UPLOAD_PROGRESS" value="'.$session->id.'"/>';
				} else {
					echo '<form name="ajax_file_upload" method="post" action="'.$action.'" enctype="multipart/form-data">';
				}
				echo '<input type="file" name="file1"/> ';
				echo '<input type="submit" class="button" value="Upload"/>';
				echo '</form>';
				echo '</div>';
				if ($this->apc_uploads) {
					echo '<div id="file_gadget_apc_progress" style="display:none">';
					echo '</div>';
				}
			}
		}

		if (($fileType == FILETYPE_USERFILE && !$categoryId) ||
				($fileType == FILETYPE_WIKI)
				)
		{
			echo '<div id="file_gadget_category" style="display: none;">';
			if ($fileType == FILETYPE_USERFILE) echo manageCategoriesDialog(CATEGORY_USERFILE);
			if ($fileType == FILETYPE_WIKI) echo manageCategoriesDialog(CATEGORY_WIKIFILE);
			echo '</div>';
		}

		echo '</div>';
	}

	/* Visar bara thumbnails. klicka en thumbnail för att visa hela bilden i 'image_big' div:en */
	function showThumbnails($fileType, $categoryId)
	{
		global $config, $session, $db;
		if (!is_numeric($fileType)) return false;

		$list = $db->getArray('SELECT * FROM tblFiles WHERE categoryId='.$categoryId.' AND fileType='.$fileType.' ORDER BY timeUploaded ASC');

		if (!$list) {
			echo 'No thumbnails to show!';
			return;
		}

		echo '<div id="image_thumbs_scroll_up" onclick="scroll_element_content(\'image_thumbs_scroller\', -'.($this->thumb_default_height*3).');"></div>';
		echo '<div id="image_thumbs_scroll_down" onclick="scroll_element_content(\'image_thumbs_scroller\', '.($this->thumb_default_height*3).');"></div>';
		echo '<div id="image_thumbs_scroller">';

		//show thumbnail of each image
		echo '<div class="thumbnails_gadget">';
		foreach ($list as $row) {
			if (in_array($row['fileMime'], $this->image_mime_types)) {
				echo '<div class="thumbnails_gadget_entry" id="thumb_'.$row['fileId'].'" onclick="loadImage('.$row['fileId'].', \'image_big\');"><center>';
				echo makeThumbLink($row['fileId'], $row['fileName']);
				echo '</center></div>';
			}
		}
		echo '</div>';
		echo '</div>'; //id="image_thumbs_scroller"

		echo '<div id="image_comments">';
		echo '<iframe id="image_comments_iframe" width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" src="'.$config['core_web_root'].'api/html_imgcomments.php?i='.$list[0]['fileId'].getProjectPath().'"></iframe>';
		echo '</div>';

		echo '<div id="image_big_holder">';

		echo '<div id="image_big">'.makeImageLink($list[0]['fileId'], $list[0]['fileName']).'</div>';

		echo '</div>';	//id="image_big_holder"
	}
	
	/* shows attachments. used to show files attached to a forum post */
	function showAttachments($_type, $_owner)
	{
		global $config;

		$list = $this->getFiles($_type,  $_owner);

		if (count($list)) {
			echo '<hr/>';
			echo 'Attached files:<br/>';
			foreach ($list as $row) {
				$show_text = $row['fileName'].' ('.formatDataSize($row['fileSize']).')';
				echo '<a href="'.$config['core_web_root'].'api/file_pt.php?id='.$row['fileId'].getProjectPath().'" target="_blank">';
				if (in_array($row['fileMime'], $this->image_mime_types)) {
					echo makeThumbLink($row['fileId'], $show_text).'</a> ';
				} else {
					echo $show_text.'</a><br/>';
				}
			}
		}
	}

	function deleteFile($_id)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_id)) return false;

		if ($session->isAdmin) {
			$q = 'DELETE FROM tblFiles WHERE fileId='.$_id;
		} else {
			$q = 'DELETE FROM tblFiles WHERE fileId='.$_id.' AND ownerId='.$session->id;
		}

		if (!$db->delete($q)) return false;

		//physically remove the file from disk
		unlink($this->upload_dir.$_id);
		$this->clearThumbs($_id);
	}

	/* Deletes all thumbnails for this file ID */
	function clearThumbs($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$dir = scandir($this->thumbs_dir);
		foreach ($dir as $name)
		{
			if (strpos($name, $_id.'_') !== false) {
				unlink($this->thumbs_dir.$name);
			}
		}
		//$session->log('Thumbs for '.$_id.' deleted');
	}


	/* Stores uploaded file associated to $session->id */
	function handleUpload($FileData, $fileType, $ownerId, $categoryId = 0)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId)) return false;

		//ignore empty file uploads
		if (!$FileData['name']) return;

		if (!is_uploaded_file($FileData['tmp_name'])) {
			$session->error = 'File upload error';
			$session->log('Attempt to upload too big file');
			return false;
		}

		$enc_filename = basename(strip_tags($FileData['name']));
		$enc_mimetype = strip_tags($FileData['type']);

		$filesize = filesize($FileData['tmp_name']);
		//fixme: flytta inserten till handleImageUpload / handleGeneralUpload istället. då slipper vi göra en UPDATE vid resizad image upload
  	$q = 'INSERT INTO tblFiles SET fileName="'.$db->escape($enc_filename).'",fileSize='.$filesize.',fileMime="'.$db->escape($enc_mimetype).'", ownerId='.$ownerId.',uploaderId='.$session->id.',uploaderIP='.$session->ip.',timeUploaded=NOW(),fileType='.$fileType.',categoryId='.$categoryId;
  	$fileId = $db->insert($q);

		//Identify and handle various types of files
		if (in_array($FileData['type'], $this->image_mime_types)) {
			$this->handleImageUpload($fileId, $FileData);
		} else if (in_array($FileData['type'], $this->audio_mime_types)) {
			$this->handleGeneralUpload($fileId, $FileData);
		} else {
			$this->handleGeneralUpload($fileId, $FileData);
		}

		return $fileId;
	}

	function handleGeneralUpload($fileId, $FileData)
	{
		global $db, $session;

		switch ($FileData['type']) {
			case 'image/bmp':	//IE 7, Firefox 2, Opera 9.2
				if (!$this->image_convert) break;
				$out_tempfile = 'c:\core_outfile.jpg';
				$check = $this->convertImage($FileData['tmp_name'], $out_tempfile, 'image/jpeg');
				if (!$check) {
					$session->log('Failed to convert bmp to jpeg!');
					break;
				}

				unlink($FileData['tmp_name']);
				rename($out_tempfile, $FileData['tmp_name']);
				$filesize = filesize($FileData['tmp_name']);
				$q = 'UPDATE tblFiles SET fileMime="image/jpeg", fileName="'.$db->escape(basename(strip_tags($FileData['name']))).'.jpg",fileSize='.$filesize.' WHERE fileId='.$fileId;
				$db->query($q);
				$this->handleImageUpload($fileId, $FileData);
				return true;

			case 'image/svg+xml':	//IE 7, Firefox 2
			case 'image/svg-xml':	//Opera 9.2
				if (!$this->image_convert) break;
				$out_tempfile = 'c:\core_outfile.png';

				$check = $this->convertImage($FileData['tmp_name'], $out_tempfile, 'image/png');

				if (!$check) {
					$session->log('Failed to convert svg to png!');
					break;
				}

				unlink($FileData['tmp_name']);
				rename($out_tempfile, $FileData['tmp_name']);
				$filesize = filesize($FileData['tmp_name']);
				$q = 'UPDATE tblFiles SET fileMime="image/png", fileName="'.$db->escape(basename(strip_tags($FileData['name']))).'.png",fileSize='.$filesize.' WHERE fileId='.$fileId;
				$db->query($q);
				$this->handleImageUpload($fileId, $FileData);
				break;
		}

		//Move the uploaded file to upload directory
		$uploadfile = $this->upload_dir.$fileId;
		if (move_uploaded_file($FileData['tmp_name'], $uploadfile)) {
			chmod($uploadfile, 0777);
			return true;
		}
		$session->log('Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile);
		return false;
	}

	/* Handle image upload, used internally only */
	function handleImageUpload($fileId, $FileData)
	{
		global $db, $session;

		list($img_width, $img_height) = getimagesize($FileData['tmp_name']);

		//Resize the image if it is too big, overwrite the uploaded file
		if (($img_width > $this->image_max_width) || ($img_height > $this->image_max_height))
		{
			$this->resizeImage($FileData['tmp_name'], $FileData['tmp_name'], $this->image_max_width, $this->image_max_height, $fileId);
		}

		//create default sized thumbnail
		$thumb_filename = $this->thumbs_dir.$fileId.'_'.$this->thumb_default_width.'x'.$this->thumb_default_height;
		$this->resizeImage($FileData['tmp_name'], $thumb_filename, $this->thumb_default_width, $this->thumb_default_height);

		//Move the uploaded file to upload directory
		$uploadfile = $this->upload_dir.$fileId;
		if (move_uploaded_file($FileData['tmp_name'], $uploadfile)) return $fileId;
		$session->log('Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile);
	}

	/* Returns array(width, height) resized to maximum $to_width and $to_height while keeping aspect ratio */
	function resizeImageCalc($filename, $to_width, $to_height)
	{
		list($orig_width, $orig_height) = getimagesize($filename);

		$max_width = $this->image_max_width;
		$max_height = $this->image_max_height;

		if ($to_width && ($to_width < $max_width)) $max_width = $to_width;
		if ($to_height && ($to_height < $max_height)) $max_height = $to_height;

		//Proportionally resize the image to the max sizes specified above
		$x_ratio = $max_width / $orig_width;
		$y_ratio = $max_height / $orig_height;

		if (($orig_width <= $max_width) && ($orig_height <= $max_height))
		{
			return Array($orig_width, $orig_height);
		}
		elseif (($x_ratio * $orig_height) < $max_height)
		{
			return Array($max_width, ceil($x_ratio * $orig_height));
		}

		return Array(ceil($y_ratio * $orig_width), $max_height);
	}

	function resizeImage($in_filename, $out_filename, $to_width = 0, $to_height = 0, $fileId = 0)
	{
		global $db;
		if (empty($to_width) && empty($to_height)) return false;

		$data = getimagesize($in_filename);
		$orig_width = $data[0];
		$orig_height = $data[1];
		$mime_type = $data['mime'];
		if (!$orig_width || !$orig_height) return false;

		//Calculate the real width & height to resize too (within $to_width & $to_height), while keeping aspect ratio
		list($tn_width, $tn_height) = $this->resizeImageCalc($in_filename, $to_width, $to_height);

		//echo 'Resizing from '.$orig_width.'x'.$orig_height.' to '.$tn_width.'x'.$tn_height.'<br/>';

		switch ($mime_type)
		{
   		case 'image/png':	$image = imagecreatefrompng($in_filename); break;
   		case 'image/jpeg': $image = imagecreatefromjpeg($in_filename); break;
   		case 'image/gif': $image = imagecreatefromgif($in_filename); break;
   		default: die('Unsupported image type '.$mime_type);
		}

		$image_p = imagecreatetruecolor($tn_width, $tn_height);

		if ($this->resample_resized) {
			imagecopyresampled($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		} else {
			imagecopyresized($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		}

		switch ($mime_type)
		{
   		case 'image/png':	imagepng($image_p, $out_filename); break;
   		case 'image/jpeg': imagejpeg($image_p, $out_filename, $this->image_jpeg_quality); break;
   		case 'image/gif': imagegif($image_p, $out_filename); break;
   		default: die('Unsupported image type '.$mime_type);
		}

		imagedestroy($image);
		imagedestroy($image_p);
		
		if ($fileId) {
			//Update fileId entry with the new file size (DONT use when creating thumbnails!)
			clearstatcache();	//needed to get current filesize()
			$q = 'UPDATE tblFiles SET fileSize='.filesize($out_filename).' WHERE fileId='.$fileId;
			$db->update($q);
		}
		
		return true;
	}

	/* Uses ImageMagick commandline image converter */
	function convertImage($src_file, $dst_file, $dst_mime_type)
	{
		switch ($dst_mime_type)
		{
			case 'image/jpeg':
				$c = 'convert -quality '.$this->image_jpeg_quality.' '.$src_file.' JPG:'.$dst_file;
				break;

			case 'image/png':
				$c = 'convert '.$src_file.' PNG:'.$dst_file;
				break;

			case 'image/gif':
				$c = 'convert '.$src_file.' GIF:'.$dst_file;
				break;

			default:
				echo 'Unknown mime type for convertImage: '.$dst_mime_type.'<br/>';
				return false;
		}
		exec($c);

		if (!file_exists($dst_file)) return false;

		return true;
	}

	/* fetches or calculates checksums for the file in tblChecksums */
	function checksums($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		$q = 'SELECT * FROM tblChecksums WHERE fileId='.$_id;
		$cached = $db->getOneRow($q);
		if ($cached) return $cached;

		$new['sha1'] = $db->escape(hash_file('sha1', $this->upload_dir.$_id));	//40-character hex string
		$new['md5'] = $db->escape(hash_file('md5', $this->upload_dir.$_id));		//32-character hex string
		//fixme: den korrekta crc32 summan generas inte.. orkar inte felsöka
		$new['crc32'] = $db->escape(hash_file('crc32', $this->upload_dir.$_id));	//8-character hex string

		$q = 'INSERT INTO tblChecksums SET fileId='.$_id.', sha1="'.$new['sha1'].'", md5="'.$new['md5'].'", crc32="'.$new['crc32'].'", timeCreated=NOW()';
		$db->insert($q);

		return $new;
	}

	/* returns sha1 checksum of file $_id. forces checksum generation if missing */
	function sha1($_id)
	{
		$sums = $this->checksums($_id);
		return $sums['sha1'];
	}

	/* used for file processing. generates a new file entry referencing to entry $_id. returns new id */
	function cloneEntry($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$file = $this->getFileInfo($_id);
		if (!$file) return false;

		$q = 'INSERT INTO tblFiles SET ownerId='.$_id.',fileType='.FILETYPE_PROCESS_CLONE.',timeUploaded=NOW()';
		return $db->insert($q);
	}

	//These headers allows the browser to cache the output for 30 days. Works with MSIE6 and Firefox 1.5
	function setCachedHeaders()
	{
		header('Expires: ' . date("D, j M Y H:i:s", time() + (86400 * 30)) . ' UTC');
		header('Cache-Control: Public');
		header('Pragma: Public');
	}

	function setNoCacheHeaders()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	}

	/* Performs an image rotation and then pass on the result to the user */
	function imageRotate($_id, $_angle)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_id) || !is_numeric($_angle)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		if (!in_array($data['fileMime'], $this->image_mime_types)) return false;

		header('Content-Type: '.$data['fileMime']);
		header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		header('Content-Transfer-Encoding: binary');

		$filename = $this->upload_dir.$_id;

		switch ($data['fileMime'])
		{
   		case 'image/png':	$image = imagecreatefrompng($filename); break;
   		case 'image/jpeg': $image = imagecreatefromjpeg($filename); break;
   		case 'image/gif': $image = imagecreatefromgif($filename); break;
   		default: die('Unsupported image type '.$data['fileMime']);
		}

		$rotated = imagerotate($image, $_angle, 0);

		switch ($data['fileMime'])
		{
   		case 'image/png':	imagepng($rotated, $filename); imagepng($rotated); break;
   		case 'image/jpeg': imagejpeg($rotated, $filename, $this->image_jpeg_quality); imagejpeg($rotated); break;
   		case 'image/gif': imagegif($rotated, $filename); imagegif($rotated); break;
   		default: die('Unsupported image type '.$data['fileMime']);
		}

		imagedestroy($image);
		imagedestroy($rotated);

		$this->clearThumbs($_id);
	}

	//takes get parameter 'dl' to send the file as an attachment
	function sendFile($_id, $force_mime = false)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		/* This sends files without extension etc as plain text if you didnt specify to download them */
		if (!$force_mime && (!isset($_GET['dl']) || $data['fileMime'] == 'application/octet-stream')) {
			header('Content-Type: text/plain');
		} else {
			header('Content-Type: '.$data['fileMime']);
		}

		if (!$force_mime && isset($_GET['dl'])) {
			/* Prompts the user to save the file */
			header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
		} else {
			/* Displays the file in the browser, and assigns a filename for the browser's "save as..." features */
			header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		}

		header('Content-Transfer-Encoding: binary');

		//Serves the file differently depending on what kind of file it is
		if (!$force_mime && in_array($data['fileMime'], $this->image_mime_types)) {
			//Generate resized image if needed
			$this->sendImage($_id);
		} else {
			$this->setCachedHeaders();

			//Just delivers the file as-is
			header('Content-Length: '. $data['fileSize']);
			echo file_get_contents($this->upload_dir.$_id);
		}

		//Count the file downloads
		if ($this->count_file_views) {
			$db->query('UPDATE tblFiles SET cnt=cnt+1 WHERE fileId='.$_id);
		}

		die;
	}

	function sendTextfile($filename)
	{
		//required for IE6:
		header('Cache-Control: cache, must-revalidate');

		//header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($realFileName)) . ' GMT');
		header('Content-Length: '.filesize($filename));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'.basename($filename).'"');

		readfile($filename, 'r');
	}

	//takes get parameters 'w' and 'h'
	function sendImage($_id)
	{
		global $session;

		$filename = $this->upload_dir.$_id;
		if (!file_exists($filename)) die('file not found');

		$temp = getimagesize($filename);

		$img_width = $temp[0];
		$img_height = $temp[1];
		$mime_type = $temp['mime'];

		$width = 0;
		if (!empty($_GET['w']) && is_numeric($_GET['w'])) $width = $_GET['w'];
		if (($width < 10) || ($width > 1500)) $width = 0;

		$height = 0;
		if (!empty($_GET['h']) && is_numeric($_GET['h'])) $height = $_GET['h'];
		if (($height < 10) || ($height > 1500)) $height = 0;

		if ($width && (($width < $img_width) || ($height < $img_height)) )  {
			/* Look for cached thumbnail */

			$out_filename = $this->thumbs_dir.$_id.'_'.$width.'x'.$height;

			if (!file_exists($out_filename)) {
				//Thumbnail of this size dont exist, create one
				$this->resizeImage($filename, $out_filename, $width, $height);
			}
		} else {
			$out_filename = $filename;
		}

		if (filemtime($out_filename) < $session->started) {
			$this->setCachedHeaders();
		} else {
			$this->setNoCacheHeaders();
		}
		header('Content-Type: '.$mime_type);
		header('Content-Length: '.filesize($out_filename));
		echo file_get_contents($out_filename);
	}

	function getFiles($fileType, $ownerId, $categoryId = 0)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId)) return array();
		
		if ($fileType == FILETYPE_FORUM) {
			$q  = 'SELECT * FROM tblFiles ';
			$q .= 'WHERE fileType='.$fileType.' AND ownerId='.$ownerId.' AND uploaderId='.$session->id;

		} else {
			$q  = 'SELECT t1.*,t2.userName AS uploaderName FROM tblFiles AS t1 ';
			$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.uploaderId=t2.userId) ';
			$q .= 'WHERE t1.categoryId='.$categoryId.' AND t1.ownerId='.$ownerId;
			$q .= ' AND t1.fileType='.$fileType;
			$q .= ' ORDER BY t1.timeUploaded ASC';
		}
		return $db->getArray($q);
	}

	function getFileCount($fileType, $ownerId, $categoryId = 0)
	{
		global $db;
		if (!is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId)) return 0;

		$q = 'SELECT COUNT(fileId) FROM tblFiles WHERE categoryId='.$categoryId.' AND fileType='.$fileType.' AND ownerId='.$ownerId;
		return $db->getOneItem($q, true);
	}

	function getFileInfo($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT t1.*,t2.userName AS uploaderName FROM tblFiles AS t1 '.
					'LEFT JOIN tblUsers AS t2 ON (t1.uploaderId=t2.userId) '.
					'WHERE t1.fileId='.$_id;

		return $db->getOneRow($q);
	}

	/* Används av ajax filen core/ajax_fileinfo.php för att visa fil-detaljer för den fil som är inzoomad just nu*/
	function showFileInfo($_id)
	{
		global $session;

		$file = $this->getFileInfo($_id);
		if (!$file) return false;

		echo 'Name: '.strip_tags($file['fileName']).'<br/>';
		echo 'Filesize: '.formatDataSize($file['fileSize']).'<br/>';
		echo 'Uploader: '.htmlentities($file['uploaderName']).'<br/>';
		echo 'At: '.$file['timeUploaded'].' ('.ago($file['timeUploaded']).')<br/>';
		if ($this->count_file_views) echo 'Downloaded: '.$file['cnt'].' times<br/>';
		if ($session->isAdmin) {
			echo 'Mime type: '.$file['fileMime'].'<br/>';
		}

		if (in_array($file['fileMime'], $this->image_mime_types))
		{
			//Show additional information for image files
			list($img_width, $img_height) = getimagesize($this->upload_dir.$_id);
			echo 'Width: '.$img_width.', Height: '.$img_height.'<br/>';
		}
		else if (in_array($file['fileMime'], $this->audio_mime_types) && extension_loaded('id3'))
		{
			//Show additional information for audio files
			echo '<h3>id3 tag</h3>';
			$id3 = @id3_get_tag($this->upload_dir.$_id, ID3_V2_2);	//note: the warning suppress was because the wip plugin caused a warning sometime on parsing id. maybe unneeded when you read this
			d($id3);
		}

		//display checksums, if any
		$arr = $this->checksums($_id);
		echo '<h3>Checksums</h3>';
		echo '<pre>';
		echo 'generated: '.$arr['timeCreated']."\n";
		echo 'sha1: '.$arr['sha1']."\n";
		echo 'md5: '.$arr['md5']."\n";
		echo 'crc32: '.$arr['crc32']."\n";
		echo 'size: '.$file['fileSize'].' bytes';
		echo '</pre>';
	}

	function getUploader($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT uploaderId FROM tblFiles WHERE fileId='.$_id;
		return $db->getOneItem($q);
	}

	function showImageGadgetXHTML()
	{
		global $config, $session;
?>
<div id="zoom_image_layer" style="display:none">
	<center>
		<input type="button" class="button_bold" value="Close" onclick="zoom_hide_elements()"/>
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/><br/>

		<img id="zoom_image" src="<?=$config['core_web_root']?>gfx/ajax_loading.gif" alt="Image"/><br/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Cut" onclick="cut_selected_file()"/>
		<input type="button" class="button" value="Resize" onclick="resize_selected_file()"/>
		<input type="button" class="button" value="Rotate left" onclick="rotate_selected_file(90)"/>
		<input type="button" class="button" value="Rotate right" onclick="rotate_selected_file(-90)"/>
		<input type="button" class="button" value="Move image" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete image" onclick="delete_selected_file()"/>
<? } ?>
<? if (!empty($config['news']['allow_rating'])) { ?>
		<br/>
		<div class="image_rate">
		<?
			//ratingGadget(RATE_IMAGE, 1)
			//fixme: to implement image rating here we need to use ajax in the rating gadget, because we need to respect "selected file"
		?>
		</div>
<? } ?>
	</center>
</div>
<?
	}

	function showAudioGadgetXHTML()
	{
		global $session;
?>
<div id="zoom_audio_layer" style="display:none">
	<center>
		<div id="zoom_audio" style="width: 160px; height: 50px;"></div>
		<br/>
		<input type="button" class="button_bold" value="Close" onclick="zoom_hide_elements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete" onclick="delete_selected_file()"/>
<? } ?>
	</center>
</div>
<?
	}
	
	function showVideoGadgetXHTML()
	{
		global $session;
?>
<div id="zoom_video_layer" style="display:none">
	<center>
		<input type="button" class="button_bold" value="Close" onclick="zoom_hide_elements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/><br/>

		<div id="zoom_video" style="width: 160px; height: 50px;"></div>

<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete" onclick="delete_selected_file()"/>
<? } ?>
	</center>
</div>
<?
	}

	function showDocumentGadgetXHTML()
	{
		global $session;
?>
<div id="zoom_file_layer" style="display:none">
	<center>
		<input type="button" class="button_bold" value="Close" onclick="zoom_hide_elements()"/> 
		<input type="button" class="button" value="Download" onclick="download_selected_file()"/>
		<input type="button" class="button" value="Pass thru" onclick="passthru_selected_file()"/>
<? if ($session->isAdmin) { ?>
		<input type="button" class="button" value="Move" onclick="move_selected_file()"/>
		<input type="button" class="button" value="Delete" onclick="delete_selected_file()"/>
<? } ?>
	</center>
</div>
<?
	}
}