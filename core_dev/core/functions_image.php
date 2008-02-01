<?
/**
 * $Id$
 *
 * Image handling helper functions
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

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

 $image_max_width			= 1100;			///<bigger images will be resized to this size
	 $image_max_height			= 900;


		$max_width = $image_max_width;
		$max_height = $image_max_height;

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
?>