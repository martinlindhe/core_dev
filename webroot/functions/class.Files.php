<?
/*
	Files class - Handle file upload, image manipulating, file management

	Written by Martin Lindhe, 2007

	todo:
		- cleanup, vissa funktioner är nog överflödiga. se igenom all kod

	todo file viewern:
		- tooltip hover på en bild med mer fil-detaljer (bredd, höjd, storlek)

		- all bakgrund blir utgråad (todo: bilden blir åxå transparent)
			kika bildvisare: http://www.mameworld.net/gurudumps/
			i detta läge visas även en rad knappar på botten: rotate, resize, save, previous, next, start/stop slideshow image

		- visa en papperskorg, kunna drag-droppa en fil och släppa i papperskorgen (ta bort med ajax)

		- ajax file upload, submitta file-formuläret i bakgrunden,när man får "ok" svar från servern,
			så läggs en <div> till som visar thumbnail på nyss uppladdad bild.

		- visa animerad gif medans filen laddas upp (senare: progress meter)

*/

require_once('functions_files.php');

class Files
{
	private $upload_dir = '/tmp/';
	private $thumbs_dir = '/tmp/';
	private $allowed_image_types	= array('jpg', 'jpeg', 'png', 'gif');
	private $allowed_audio_types	= array('mp3');

	private $image_max_width			= 800;	//bigger images will be resized to this size	
	private $image_max_height			= 600;
	private $thumb_default_width	= 100;
	private $thumb_default_height	= 80;
	private $image_jpeg_quality		= 70;		//0-100% quality for recompression of very large uploads (like digital camera pictures)
	private $resample_resized			= true;	//use imagecopyresampled() instead of imagecopyresized() to create better-looking thumbnails

	public function __construct(array $files_config)
	{
		if (isset($files_config['upload_dir'])) $this->upload_dir = $files_config['upload_dir'];
		if (isset($files_config['thumbs_dir'])) $this->thumbs_dir = $files_config['thumbs_dir'];
		if (isset($files_config['allowed_image_types'])) $this->allowed_image_types = $files_config['allowed_image_types'];
		if (isset($files_config['allowed_audio_types'])) $this->allowed_audio_types = $files_config['allowed_audio_types'];
		
		if (isset($files_config['image_max_width'])) $this->image_max_width = $files_config['image_max_width'];
		if (isset($files_config['image_max_height'])) $this->image_max_height = $files_config['image_max_height'];

		if (isset($files_config['thumb_default_width'])) $this->thumb_default_width = $files_config['thumb_default_width'];
		if (isset($files_config['thumb_default_height'])) $this->thumb_default_height = $files_config['thumb_default_height'];
	}


	public function showFiles()
	{
		global $session, $db;
		
		if (!$session->id) return;

		if (!empty($_FILES['file1'])) $this->handleUserUpload($_FILES['file1']);

		echo '<div class="file_gadget">';

		//menu
		echo '<div class="file_gadget_header">File Gadget Overview</div>';

		$list = $db->GetArray('SELECT * FROM tblFiles WHERE ownerId='.$session->id.' AND fileType='.FILETYPE_NORMAL_UPLOAD);
		
		echo '<div class="file_gadget_content">';
		for ($i=0; $i<count($list); $i++)
		{
			list($file_firstname, $file_lastname) = explode('.', strtolower($list[$i]['fileName']));

			if (in_array($file_lastname, $this->allowed_image_types)) {
				//show thumbnail of image
				echo '<div class="file_gadget_entry" id="file_'.$list[$i]['fileId'].'" onClick="zoomImage('.$list[$i]['fileId'].');"><center>';
				echo '<img src="file.php?id='.$list[$i]['fileId'].'&amp;w='.$this->thumb_default_width.'&amp;h='.$this->thumb_default_height.'" alt="Thumbnail" title="'.$list[$i]['fileName'].'">';
				echo '</center></div>';
			} else if (in_array($file_lastname, $this->allowed_audio_types)) {
				//show icon for audio files
				echo '<div class="file_gadget_entry" id="file_'.$list[$i]['fileId'].'"><center>';
				echo '<img src="/gfx/icon_audio_32.png" width=80 height=80 alt="Audio file" title="'.$list[$i]['fileName'].'">';
				echo '</center></div>';
			} else {
				die('todo: '. $list[$i]['fileMime']);
			}
		}
		echo '</div>';
		
		echo '<br><br><br>';

		echo '<div class="file_gadget_upload">';
		echo '<form name="ajax_show_files" action="" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file1"> ';
		echo '<input type="submit" class="button" value="Upload">';
		echo '</form>';
		echo '</div>';
		
		echo '</div>';
	}

	/* Visar bara thumbnails. klicka en thumbnail för att visa hela bilden i 'image_big' div:en */
	public function showThumbnails($ownerId)
	{
		global $session, $db;

		$list = $db->GetArray('SELECT * FROM tblFiles WHERE ownerId='.$ownerId.' AND fileType='.FILETYPE_NORMAL_UPLOAD);

		echo '<div id="image_big_holder"><div id="image_big"><img src="file.php?id='.$list[0]['fileId'].'"></div></div>';
		echo '<div id="image_thumbs_scroll_up" onClick="scroll_element_content(\'image_thumbs_scroller\', -120);"></div>';
		echo '<div id="image_thumbs_scroll_down" onClick="scroll_element_content(\'image_thumbs_scroller\', 120);"></div>';
		echo '<div id="image_thumbs_scroller">';

		echo '<div class="thumbnails_gadget">';

		for ($i=0; $i<count($list); $i++)
		{
			list($file_firstname, $file_lastname) = explode('.', strtolower($list[$i]['fileName']));

			//show thumbnail of image
			if (in_array($file_lastname, $this->allowed_image_types)) {
				echo '<div class="thumbnails_gadget_entry" id="thumb_'.$list[$i]['fileId'].'" onClick="loadImage('.$list[$i]['fileId'].', \'image_big\');"><center>';
				echo '<img src="file.php?id='.$list[$i]['fileId'].'&amp;w='.$this->thumb_default_width.'&amp;h='.$this->thumb_default_height.'" alt="Thumbnail" title="'.$list[$i]['fileName'].'">';
				echo '</center></div>';
			}
		}

		echo '</div>';
	}

	/* Stores uploaded file associated to $session->id */
	function handleUserUpload($FileData, $categoryId = 0)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($categoryId)) return false;
		
		//ignore empty file uploads
		if (!$FileData['name']) return;

		if (!is_uploaded_file($FileData['tmp_name'])) {
			$session->error = 'File too big';
			$db->log('Attempt to upload too big file');
			return false;
		}

		$enc_filename = $db->escape(basename(strip_tags($FileData['name'])));
		$enc_mimetype = $db->escape(strip_tags($FileData['type']));

		list($file_firstname, $file_lastname) = explode('.', strtolower($enc_filename));

		$filesize = filesize($FileData['tmp_name']);

  	$sql = 'INSERT INTO tblFiles SET fileName="'.$enc_filename.'",fileSize='.$filesize.',fileMime="'.$enc_mimetype.'",ownerId='.$session->id.',uploaderId='.$session->id.',uploaderIP='.$session->ip.',timeUploaded=NOW(),fileType='.FILETYPE_NORMAL_UPLOAD.',categoryId='.$categoryId;
  	$db->query($sql);
  	$fileId = $db->insert_id;
		
		//Identify and handle various types of files
		if (in_array($file_lastname, $this->allowed_image_types)) {
			$this->handleImageUpload($FileData, $fileId);
		} else if (in_array($file_lastname, $this->allowed_audio_types)) {
			$this->handleAudioUpload($FileData, $fileId);
		} else {
			unlink($FileData['tmp_name']);
			return 'Unsupported filetype';
		}


	}

	private function handleAudioUpload($FileData, $fileId)
	{
		//nothing happening here yet
	}

	/* Handle image upload, used internally only */
	private function handleImageUpload($FileData, $fileId)
	{
		list($img_width, $img_height) = getimagesize($FileData['tmp_name']);

		//Resize the image if it is too big, overwrite the uploaded file
		if (($img_width > $this->image_max_width) || ($img_height > $this->image_max_height))
		{
			$resizedFile = $FileData['tmp_name'];
			$resizedImage = $this->resizeImage($FileData['tmp_name'], $resizedFile, $this->image_max_width, $this->image_max_height);
		}

		//create default sized thumbnail
		$thumb_filename = $this->thumbs_dir.$fileId.'_'.$this->thumb_default_width.'x'.$this->thumb_default_height;
		$this->resizeImage($FileData['tmp_name'], $thumb_filename, $this->thumb_default_width, $this->thumb_default_height);

		//Move the uploaded file to upload directory
		$uploadfile = $this->upload_dir.$fileId;
		if (move_uploaded_file($FileData['tmp_name'], $uploadfile)) return $fileId;
		$db->log('Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile);
	}
	
	
	/* Returns array(width, height) resized to maximum $to_width and $to_height while keeping aspect ratio */
	private function resizeImageCalc($filename, $to_width, $to_height)
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

	private function resizeImage($in_filename, $out_filename, $to_width=0, $to_height=0)
	{
		if (empty($to_width) && empty($to_height)) return false;

		$data = getimagesize($in_filename);	//todo, kan man använda list() ?
		$orig_width = $data[0];
		$orig_height = $data[1];
		$mime_type = $data['mime'];
		if (!$orig_width || !$orig_height) return false;

		//Calculate the real width & height to resize too (within $to_width & $to_height), while keeping aspect ratio
		list($tn_width, $tn_height) = $this->resizeImageCalc($in_filename, $to_width, $to_height);

		//echo 'Resizing from '.$orig_width.'x'.$orig_height.' to '.$tn_width.'x'.$tn_height.'<br>';

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
	}

	//These headers allows the browser to cache the output for 30 days. Works with MSIE6 and Firefox 1.5
	function setCachedHeaders()
	{
		header('Expires: ' . date("D, j M Y H:i:s", time() + (86400 * 30)) . ' UTC');
		header('Cache-Control: Public');
		header('Pragma: Public');
	}


	//Note: These header commands have been verified to work with IE6 and Firefox 1.5 only, no other browsers have been tested
	function sendFile($fileId, $download)
	{
		if (!is_numeric($fileId)) return false;

		global $db;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$fileId);
		if (!$data) die;

		list($file_firstname, $file_lastname) = explode('.', strtolower($data['fileName']));

		/* This sends files without extension etc as plain text if you didnt specify to download them */
		if (!$download && (($data['fileMime'] == 'application/octet-stream') || !$file_lastname)) {
			header('Content-Type: text/plain');
		} else {
			header('Content-Type: '.$data['fileMime']);
		}

		if ($download) {
			/* Prompts the user to save the file */
			header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
		} else {
			/* Displays the file in the browser, and assigns a filename for the browser's "save as..." features */
			header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		}

		header('Content-Transfer-Encoding: binary');

		//Serves the file differently depending on what kind of file it is
		if (in_array($file_lastname, $this->allowed_image_types)) {
			//Generate resized image if needed
			$this->sendImage($data['fileId']);
		} else {
			$this->setCachedHeaders();

			//Just delivers the file as-is
			header('Content-Length: '. $data['fileSize']);
			echo file_get_contents($this->upload_dir.$fileId);
		}
	}
	
	private function sendImage($fileId)
	{
		global $session;

		$filename = $this->upload_dir.$fileId;
		list($img_width, $img_height) = getimagesize($filename);

		$width = 0;
		if (!empty($_GET['w']) && is_numeric($_GET['w'])) $width = $_GET['w'];
		if (($width < 10) || ($width > 1500)) $width = 0;

		$height = 0;
		if (!empty($_GET['h']) && is_numeric($_GET['h'])) $height = $_GET['h'];
		if (($height < 10) || ($height > 1500)) $height = 0;

		if ($width && (($width < $img_width) || ($height < $img_height)) )  {
			/* Look for cached thumbnail */

			$out_filename = $this->thumbs_dir.$fileId.'_'.$width.'x'.$height;

			if (!file_exists($out_filename)) {
				//Thumbnail of this size dont exist, create one
				$this->resizeImage($filename, $out_filename, $width, $height);
			}
		} else {
			$out_filename = $filename;
		}

		if (filemtime($out_filename) < $session->started) {
			$this->setCachedHeaders();
		}

		header('Content-Length: '.filesize($out_filename));
		echo file_get_contents($out_filename);
	}
	
}