<?
/**
 * $Id$
 *
 * Image handling helper functions
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	$config['image']['resample_resized']	= true;		///< use imagecopyresampled() instead of imagecopyresized() to create better-looking thumbnails
	$config['image']['jpeg_quality']			= 70;			///< 0-100% quality for recompression of very large uploads (like digital camera pictures)

	/**
	 * Resizes specified image file
	 *
	 * \param $in_filename
	 * \param $out_filename
	 * \param $to_width
	 * \param $to_height
	 * \param $fileId
	 */
	function resizeImage($in_filename, $out_filename, $to_width = 0, $to_height = 0, $fileId = 0)
	{
		global $db, $config;
		if (empty($to_width) && empty($to_height)) return false;

		$data = getimagesize($in_filename);
		$orig_width = $data[0];
		$orig_height = $data[1];
		$mime_type = $data['mime'];
		if (!$orig_width || !$orig_height) return false;

		//Calculate the real width & height to resize too (within $to_width & $to_height), while keeping aspect ratio
		list($tn_width, $tn_height) = resizeImageCalc($in_filename, $to_width, $to_height);

		//echo 'Resizing from '.$orig_width.'x'.$orig_height.' to '.$tn_width.'x'.$tn_height.'<br/>';

		switch ($mime_type)
		{
   		case 'image/png':	$image = imagecreatefrompng($in_filename); break;
   		case 'image/jpeg': $image = imagecreatefromjpeg($in_filename); break;
   		case 'image/gif': $image = imagecreatefromgif($in_filename); break;
   		default: die('Unsupported image type '.$mime_type);
		}

		$image_p = imagecreatetruecolor($tn_width, $tn_height);

		if ($config['image']['resample_resized']) {
			imagecopyresampled($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		} else {
			imagecopyresized($image_p, $image, 0,0,0,0, $tn_width, $tn_height, $orig_width, $orig_height);
		}

		switch ($mime_type)
		{
   		case 'image/png':	imagepng($image_p, $out_filename); break;
   		case 'image/jpeg': imagejpeg($image_p, $out_filename, $config['image']['jpeg_quality']); break;
   		case 'image/gif': imagegif($image_p, $out_filename); break;
   		default: die('Unsupported image type '.$mime_type);
		}

		imagedestroy($image);
		imagedestroy($image_p);
		
		if ($fileId) {
			//Update fileId entry with the new file size (DONT use when creating thumbnails or cloning files!)
			clearstatcache();	//needed to get current filesize()
			$q = 'UPDATE tblFiles SET fileSize='.filesize($out_filename).' WHERE fileId='.$fileId;
			$db->update($q);
		}
		
		return true;
	}

	/**
	 * Utility function, Returns array(width, height) resized to maximum $to_width and $to_height while keeping aspect ratio
	 *
	 * \param $filename image file to calculate new size for
	 * \param $to_width
	 * \param $to_height
	 * \return new width & height
	 */
	function resizeImageCalc($filename, $to_width, $to_height)
	{
		list($orig_width, $orig_height) = getimagesize($filename);

		//Proportionally resize the image to the max sizes specified above
		$x_ratio = $to_width / $orig_width;
		$y_ratio = $to_height / $orig_height;

		if (($orig_width <= $to_width) && ($orig_height <= $to_height))
		{
			return Array($orig_width, $orig_height);
		}
		elseif (($x_ratio * $orig_height) < $to_height)
		{
			return Array($to_width, ceil($x_ratio * $orig_height));
		}

		return Array(ceil($y_ratio * $orig_width), $to_height);
	}

	/**
	 * Converts a image to specified file type. Currently supports conversions to jpeg, png or gif
	 * Requires ImageMagick commandline image converter "convert" installed
	 *
	 * \param $src_file
	 * \param $dst_file
	 * \param $dst_mime_type
	 */
	function convertImage($src_file, $dst_file, $dst_mime_type)
	{
		switch ($dst_mime_type)
		{
			case 'image/jpeg':
				$c = 'convert -quality '.$this->image_jpeg_quality.' '.escapeshellarg($src_file).' JPG:'.escapeshellarg($dst_file);
				break;

			case 'image/png':
				$c = 'convert '.escapeshellarg($src_file).' PNG:'.escapeshellarg($dst_file);
				break;

			case 'image/gif':
				$c = 'convert '.escapeshellarg($src_file).' GIF:'.escapeshellarg($dst_file);
				break;

			default:
				echo 'convertImage(): Unknown destination mimetype "'.$dst_mime_type.'"<br/>';
				return false;
		}
		echo 'Executing: '.$c.'<br/>';
		exec($c);

		if (!file_exists($dst_file)) return false;
		return true;
	}

	/**
	 * Draws text centered horizontally & vertically
	 *
	 * \param $str array of lines of text to print
	 * \param $template png image to use as template to draw the text upon
	 * \param $font specify the font to use. numeric 1-5 for gd's internal fonts, or specify a .gdf or .ttf font instead
	 * \param $col optional color to draw the font in, array(r,g,b). defaults to black
	 * \param $ttf_size optional size of ttf font, defaults to 12
	 * \return image resource
	 */
	function pngCenterText($str, $template, $font = 1, $col = array(), $ttf_size = 12 )
	{
		$ttf_angle = 0;

		$im = imagecreatefrompng($template);

		if (empty($col)) {
			$color = imagecolorallocate($im, 0, 0, 0); //default to black
		} else {
			$color = imagecolorallocate($im, $col[0], $col[1], $col[2]);
		}

		$ttf = false;
		if (!is_numeric($font)) {
			if (substr(strtolower($font), -4) == '.ttf') {
				$ttf = true;

				$fh = 0;
				foreach ($str as $txt)	//find highest font height
				{
					$x = imagettfbbox($ttf_size, $ttf_angle, $font, $txt);
					$t = $x[1] - $x[7];
					if ($t > $fh) $fh = $t;
				}

			} else {
				//GDF font handling
				$font = imageloadfont($font);
			}
		}
		
		if (!$ttf) {
			$fh = imagefontheight($font);
		}

		/*
			prints the text in $str array centered vertically & horizontally over the image
		*/
		$i = 0;

		foreach ($str as $txt)
		{
			if (!$ttf) {
				$txt = mb_convert_encoding($txt, 'ISO-8859-1', 'auto'); //FIXME required with php 5.2, as imagestring() cant handle utf8
				$fw = strlen($txt) * imagefontwidth($font);
			} else {
				$x = imagettfbbox($ttf_size, $ttf_angle, $font, $txt);
				$fw = $x[2] - $x[0];	//font width
			}

			$px = (imagesx($im) / 2) - ($fw / 2);
			$py = (imagesy($im) / 2) - ( ((count($str)/2) - $i) * $fh);

			if (!$ttf) {
				imagestring($im, $font, $px, $py, $txt, $color);
			} else {
				imagettftext($im, $ttf_size, $ttf_angle, $px, $py + $fh, $color, $font, $txt);
			}

			$i++;
		}
		return $im;
	}

	/**
	 * the gd function imagerotate() is only available in bundled gd (php windows) - FIXME check if this changes with php6!
	 * FIXME this wont take negative values for rotate left!
	 */
	function my_imagerotate($im, $angle)
	{
		if (function_exists("imagerotate")) {
			return imagerotate($im, $angle, 0);
		}

		$src_x = imagesx($im);
		$src_y = imagesy($im);
		if ($angle == 180) {
			$dest_x = $src_x;
			$dest_y = $src_y;
		}
		elseif ($src_x <= $src_y) {
			$dest_x = $src_y;
			$dest_y = $src_x;
		}
		elseif ($src_x >= $src_y) {
			$dest_x = $src_y;
			$dest_y = $src_x;
		}

		$rotate = imagecreatetruecolor($dest_x, $dest_y);
		imagealphablending($rotate, false);

		switch ($angle) {
			case -90:
			case 90:
				for ($y = 0; $y < ($src_y); $y++) {
					for ($x = 0; $x < ($src_x); $x++) {
						$color = imagecolorat($im, $x, $y);
						imagesetpixel($rotate, $y, $dest_y - $x - 1, $color);
					}
				}
				break;

			case -180:
			case 180:
				for ($y = 0; $y < ($src_y); $y++) {
					for ($x = 0; $x < ($src_x); $x++) {
						$color = imagecolorat($im, $x, $y);
						imagesetpixel($rotate, $dest_x - $x - 1, $dest_y - $y - 1, $color);
					}
				}
				break;

			case -270:
			case 270:
				for ($y = 0; $y < ($src_y); $y++) {
					for ($x = 0; $x < ($src_x); $x++) {
						$color = imagecolorat($im, $x, $y);
						imagesetpixel($rotate, $dest_x - $y - 1, $x, $color);
					}
				}
				break;

			default: $rotate = $im;
		}
		return $rotate;
	}
?>