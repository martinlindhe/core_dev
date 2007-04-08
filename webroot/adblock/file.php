<?
	//file.php - tar emot file id, returnerar filen
	//Note: These header commands have been verified to work with IE6 and Firefox 1.5 only, no other browsers have been tested
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$download = 0;
	if (isset($_GET['dl'])) $download = 1;
	
	$width = 0;
	if (!empty($_GET['width']) && is_numeric($_GET['width'])) $width = $_GET['width'];
	if (($width < 10) || ($width > 1500)) $width = 0;
	
	require('config.php');

	$data = getFile($fileId);
	if (!$data) die;

	if (empty($_GET['width']) && empty($_GET['height'])) updateFileViews($fileId);	//dont count views for thumbnails
	
	$last_name = '';
	$pos = strrpos($data['fileName'], '.');
	if ($pos !== false) $last_name = substr($data['fileName'], $pos);

	/* This sends files without extension etc as plain text if you didnt specify to download them */

	if (!$download && (($data['fileMime'] == 'application/octet-stream') || !$last_name)) {
		header('Content-Type: text/plain');
	} else {
		header('Content-Type: ' . $data['fileMime']);
	}

	//These headers allows the browser to cache the images for 30 days. Works with MSIE6 and Firefox 1.5
	header("Expires: " . date("D, j M Y H:i:s", time() + (86400 * 30)) . " UTC");
	header("Cache-Control: Public");
	header("Pragma: Public");

	if ($download) {
		/* Prompts the user to save the file */
		header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
	} else {
		/* Assigns a filename for the browser's "save as..." features */
		header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
	}

	header('Content-Transfer-Encoding: binary');
	
	$filename = $config['upload_dir'].$data['fileId'];
	$img_size = getimagesize($filename);

	if ($width && !$download && $img_size && ($width < $img_size[0]))  {
		/* Look for cached thumbnail */

		$thumbs_filename = $config['thumbs_dir'].$data['fileId'].'_'.$width.'w';

		if (!file_exists($thumbs_filename))
		{
			$thumb = resizeImage($filename, $width);

			switch ($img_size['mime'])
			{
   			case 'image/png':		imagepng($thumb, $thumbs_filename); break;
	   		case 'image/jpeg':	imagejpeg($thumb, $thumbs_filename, $config['thumbnail_quality']); break;
	   		case 'image/gif':		imagegif($thumb, $thumbs_filename); break;
			}
		}

		$thumb_size = filesize($thumbs_filename);
		header('Content-Length: '. $thumb_size);
		echo file_get_contents($thumbs_filename);
		die;
			
	} else {
		header('Content-Length: '. $data['fileSize']);
		echo file_get_contents($filename);
		die;
	}
?>