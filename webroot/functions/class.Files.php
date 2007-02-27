<?
/*
	Files class - Handle file upload, image manipulating, file management

	Written by Martin Lindhe, 2007
	
	todo:
		- skapa en thumbnail av en bild direkt när den laddas upp
		
	todo: ajax file upload, submitta file-formuläret i bakgrunden,när man får "ok" svar från servern,
		så läggs en <div> till som visar thumbnail på nyss uppladdad bild.
		- visa animerad gif medans filen laddas upp (senare: progress meter)
		
	todo: cleanup, vissa funktioner är nog överflödiga. se igenom all kod

*/

require_once('functions_files.php');

class Files
{
	private $upload_dir = '/tmp/';
	private $thumbs_dir = '/tmp/';
	private $allowed_image_types	= array('jpg', 'jpeg', 'png', 'gif');
	private $allowed_audio_types	= array('mp3');

	private $image_max_width		= 1280;	//bigger images will be resized to this size	
	private $image_max_height		= 1024;
	private $thumb_default_width = 100;
	private $thumb_default_height = 100;
	private $image_jpeg_quality	= 70;		//0-100% quality for recompression of very large uploads (like digital camera pictures)
	private $resample_resized		= true;	//use imagecopyresampled() instead of imagecopyresized() to create better-looking thumbnails

	public function __construct(array $files_config)
	{
		if (isset($files_config['upload_dir'])) $this->upload_dir = $files_config['upload_dir'];
		if (isset($files_config['thumbs_dir'])) $this->thumbs_dir = $files_config['thumbs_dir'];
		if (isset($files_config['allowed_image_types'])) $this->allowed_image_types = $files_config['allowed_image_types'];
		if (isset($files_config['allowed_audio_types'])) $this->allowed_audio_types = $files_config['allowed_audio_types'];
	}


	//todo: tooltip hover på en bild med mer fil-detaljer (bredd, höjd, storlek)
	//todo: visa en papperskorg, kunna drag-droppa en fil och släppa i papperskorgen (ta bort med ajax)
	public function showFiles()
	{
		global $session, $db;

		if (!empty($_FILES['file1'])) {
			$this->handleUserUpload(FILETYPE_NORMAL_UPLOAD, $_FILES['file1']);
		}

		echo '<div class="file_gadget_holder">';
		
		//menu
		echo '<div style="width: 100%; float: left;">';
		echo '<div style="float: left; width: 150px; background-color: #886699;">Overview</div>';
		echo '<div style="float: left; width: 150px; background-color: #886699;">Details</div>';
		echo '</div>';

		$list = $db->GetArray('SELECT * FROM tblFiles WHERE ownerId='.$session->id.' AND fileType='.FILETYPE_NORMAL_UPLOAD);
		
		for ($i=0; $i<count($list); $i++)
		{
			list($file_firstname, $file_lastname) = explode('.', $list[$i]['fileName']);
			
			echo '<div id="file_'.$list[$i]['fileId'].'" class="file_gadget_entry">';
			if (in_array($file_lastname, $this->allowed_image_types)) {
				//show thumbnail of image
				echo '<a href="file.php?id='.$list[$i]['fileId'].'">';
				echo '<img src="file.php?id='.$list[$i]['fileId'].'&w=100&h=100" alt="Thumbnail" title="'.$list[$i]['fileName'].'">';
				echo '</a>';
			} else if (in_array($file_lastname, $this->allowed_audio_types)) {
				//show icon for sound files
				echo '<img src="/gfx/icon_audio_32.png" width=100 height=100 alt="Audio file" title="'.$list[$i]['fileName'].'">';
			} else {
				echo $list[$i]['fileMime'];
			}
			echo '</div>';
		}

		echo '</div>';

		echo '<div class="file_gadget_upload">';
		echo '<form name="ajax_show_files" action="" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file1">';
		echo '<input type="submit" value="Upload">';
		echo '</form>';
		echo '</div>';
		
	}

	/* Stores uploaded file associated to $session->id */
	function handleUserUpload($fileType, $FileData, $categoryId = 0)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($fileType) || !is_numeric($categoryId)) return false;

		if (!is_uploaded_file($FileData['tmp_name'])) {
			$db->log('Possible file upload attack!');
			return false;
		}

		$dbFileName = $db->escape(basename(strip_tags($FileData['name'])));
		$dbFileMime = $db->escape(strip_tags($FileData['type']));

		list($file_firstname, $file_lastname) = explode('.', $dbFileName);
		
		//Identify and handle various types of files
		if (in_array($file_lastname, $this->allowed_image_types)) {
			$this->handleImageUpload($FileData);
		} else if (in_array($file_lastname, $this->allowed_audio_types)) {
			$this->handleAudioUpload($FileData);
		} else {
			unlink($FileData['tmp_name']);
			return 'Unsupported filetype';
		}

		$fileSize = filesize($FileData['tmp_name']);

  	$sql = 'INSERT INTO tblFiles SET fileName="'.$dbFileName.'",fileSize='.$fileSize.',fileMime="'.$dbFileMime.'",ownerId='.$session->id.',uploaderId='.$session->id.',uploaderIP='.$session->ip.',timeUploaded=NOW(),fileType='.$fileType.',categoryId='.$categoryId;
  	$db->query($sql);
  	$fileId = $db->insert_id;

		$uploadfile = $this->upload_dir.$fileId;

		if (move_uploaded_file($FileData['tmp_name'], $uploadfile)) return $fileId;

		$db->log('Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile);
	}

	private function handleAudioUpload($FileData)
	{
		//nothing happening here yet
	}

	/* Handle image upload, used internally only */
	private function handleImageUpload($FileData)
	{
		list($file_firstname, $file_lastname) = explode('.',basename(strip_tags($FileData['name'])));
		list($img_width, $img_height) = getimagesize($FileData['tmp_name']);

		//Resize the image if it is too big
		if (($img_width > $this->image_max_width) || ($img_height > $this->image_max_height))
		{
			$resizedFile = $FileData['tmp_name'].'_resizetmp';
			$resizedImage = $this->resizeImage($FileData['tmp_name'], $resizedFile, $this->image_max_width, $this->image_max_height);
		}
		
		//create default sized thumbnail
		//$thumb_filename = $this->thumbs_dir..'_'.$this->thumb_default_width.'x'.$this->thumb_default_height;
		//$this->resizeImage($FileData['tmp_name'], $thumb_filename, $this->thumb_default_width, $this->thumb_default_height);
	}
	
	
	/* Returns array with width, height propotionally resized to maximum $to_width and $to_height */
	private function resizeImageCalc($filename, $to_width, $to_height)
	{
		if (!is_file($filename)) return false;

		list($orig_width, $orig_height) = getimagesize($filename);
		if (!$orig_width || !$orig_height) return false;

		$max_width = $this->image_max_width;
		$max_height = $this->image_max_height;

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

	private function resizeImage($in_filename, $out_filename, $to_width=0, $to_height=0)
	{
		if (empty($to_width) && empty($to_height)) return false;

		$data = getimagesize($in_filename);
		$orig_width = $data[0];
		$orig_height = $data[1];
		$mime_type = $data['mime'];

		list($tn_width, $tn_height) = $this->resizeImageCalc($in_filename, $to_width, $to_height);

		//echo 'Resizing from '.$orig_width.'x'.$orig_height.' to '.$tn_width.'x'.$tn_height.'<br>';

		switch ($mime_type)
		{
   		case 'image/png':	$image = imagecreatefrompng($in_filename); break;
   		case 'image/jpeg': $image = imagecreatefromjpeg($in_filename); break;
   		case 'image/gif': $image = imagecreatefromgif($in_filename); break;
   		default: echo 'Unknown mime type '.$mime_type; die;
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
   		default: echo 'Unknown mime type '.$mime_type; die;
		}

		imagedestroy($image);
	}
	
	//Note: These header commands have been verified to work with IE6 and Firefox 1.5 only, no other browsers have been tested
	function outputFile($fileId, $download)
	{
		if (!is_numeric($fileId)) return false;

		global $db;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$fileId);
		if (!$data) die;

		$last_name = '';
		$pos = strrpos($data['fileName'], '.');
		if ($pos !== false) $last_name = substr($data['fileName'], $pos);

		/* This sends files without extension etc as plain text if you didnt specify to download them */
		if (!$download && (($data['fileMime'] == 'application/octet-stream') || !$last_name)) {
			header('Content-Type: text/plain');
		} else {
			header('Content-Type: '.$data['fileMime']);
		}

		//These headers allows the browser to cache the images for 30 days. Works with MSIE6 and Firefox 1.5
		header('Expires: ' . date("D, j M Y H:i:s", time() + (86400 * 30)) . ' UTC');
		header('Cache-Control: Public');
		header('Pragma: Public');

		if ($download) {
			/* Prompts the user to save the file */
			header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
		} else {
			/* Displays the file in the browser, and assigns a filename for the browser's "save as..." features */
			header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		}

		header('Content-Transfer-Encoding: binary');
	
		list($file_firstname, $file_lastname) = explode('.', $data['fileName']);
		
		//Serves the file differently depending on what kind of file it is
		if (in_array($file_lastname, $this->allowed_image_types)) {
			//Generate resized image if needed
			$this->outputImageFile($data['fileId']);
		} else {
			//Just delivers the file as-is
			header('Content-Length: '. $data['fileSize']);
			echo file_get_contents($filename);			
		}
	}
	
	private function outputImageFile($fileId)
	{
		$filename = $this->upload_dir.$fileId;
		$img_size = getimagesize($filename);

		$width = 0;
		if (!empty($_GET['w']) && is_numeric($_GET['w'])) $width = $_GET['w'];
		if (($width < 10) || ($width > 1500)) $width = 0;

		$height = 0;
		if (!empty($_GET['h']) && is_numeric($_GET['h'])) $height = $_GET['h'];
		if (($height < 10) || ($height > 1500)) $height = 0;

		if ($width && $img_size && (($width < $img_size[0]) || ($height < $img_size[1])) )  {
			/* Look for cached thumbnail */

			$thumb_filename = $this->thumbs_dir.$fileId.'_'.$width.'x'.$height;

			if (!file_exists($thumb_filename)) {
				$this->resizeImage($filename, $thumb_filename, $width, $height);
			}

			$thumb_size = filesize($thumb_filename);
			header('Content-Length: '. $thumb_size);
			echo file_get_contents($thumb_filename);
		} else {
			$file_size = filesize($filename);
			header('Content-Length: '. $file_size);
			echo file_get_contents($filename);
		}
	}
	
}