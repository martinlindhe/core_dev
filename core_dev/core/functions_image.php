<?
/*
	functions_image.php - image handling helper functions
*/

	/* draws $str array centered horizontally & vertically, returns image resource */
	function pngCenterText($str, $template, $gdf = 1, $col = array() )
	{
		$im = imagecreatefrompng($template);

		if (empty($col)) {
			$color = imagecolorallocate($im, 0, 0, 0); //default to black
		} else {
			$color = imagecolorallocate($im, $col[0], $col[1], $col[2]);
		}

		if (!is_numeric($gdf)) {
			$font = imageloadfont($gdf);
		} else {
			$font = $gfd;
		}

		/*
			prints the text in $str array centered vertically & horizontally over the image
		*/
		$i = 0;
		foreach ($str as $txt)
		{
			$px = (imagesx($im) / 2) - ((strlen($txt)*imagefontwidth($font)) / 2);
			$py = (imagesy($im) / 2) - ( ((count($str)/2) - $i) * imagefontheight($font) );

			imagestring($im, $font, $px, $py, $txt, $color);
			$i++;
		}
		return $im;
	}

?>