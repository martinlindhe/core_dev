<?
function make_thumb($src, $dst, $dstWW = 90, $quality = 91) {
	if(!file_exists($src) || (is_dir($dst) && $dst != '')) {
		return false;
	}
 
	$info = getimagesize($src);
	switch($info[2]) {
	case 1:
		$im_src = imagecreatefromgif($src);
		break;
	case 2:
		$im_src = imagecreatefromjpeg($src);
		break;
	case 3:
		$im_src = imagecreatefrompng($src);
		break;
	}
	if($info[0] >= $dstWW) {
		$thumb_width = $dstWW;
		$thumb_height = ($info[1] * ($dstWW / $info[0]));
	} else {
		$thumb_width = ($info[0] * ($dstWW / $info[1]));
		$thumb_height = $dstWW;
	}

	$img_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
	imagecopyresampled($img_thumb, $im_src, 0, 0, 0, 0, $thumb_width, $thumb_height, $info[0], $info[1]);

	imageJPEG($img_thumb, $dst, $quality);

	ImageDestroy($img_thumb);
	ImageDestroy($im_src);
	return true;
}
function make_full($src, $dst, $quality = 92) {
    if (!file_exists($src) || (is_dir($dst) && $dst != '')) {
        return 1;
    }
    list($srcWW, $srcHH, $srcNM) = getimagesize($src);

    switch($srcNM) {
        case 1:
            $srcIMG = imagecreatefromgif($src);
            break;
        case 2:
            $srcIMG = imagecreatefromjpeg($src);
            break;
        case 3:
            $srcIMG = imagecreatefrompng($src);
            break;
    }

    $tmpIMG = imageCreateTrueColor($srcWW, $srcHH);
    //imageAntiAlias($tmpIMG, true); // kan användas om det finns.
    imagecopyresampled($tmpIMG, $srcIMG, 0, 0, 0, 0, $srcWW, $srcHH, $srcWW, $srcHH);

    $dstIMG = imageCreateTrueColor($srcWW, $srcHH);
    imagecopyresampled($dstIMG, $tmpIMG, 0, 0, 0, 0, $srcWW, $srcHH, $srcWW, $srcHH);
    imageJpeg($dstIMG, $dst, $quality);
    imagedestroy($dstIMG);
    imagedestroy($tmpIMG);
    imagedestroy($srcIMG);
	return 0;
}

function doWM($src, $dst, $quality=95, $exkl = false) {
	if(!file_exists($src) || (is_dir($dst) && $dst != '')) {
		return 1;
	}
	$info = getimagesize($src);
	switch($info[2]) {
	case 1:
		$img_src = imagecreatefromgif($src);
		break;
	case 2:
		$img_src = imagecreatefromjpeg($src);
		break;
	case 3:
		$img_src = imagecreatefrompng($src);
		break;
	}
	if($exkl)
		$img_top = imagecreatefrompng('template_exclusive.png');
	else
		$img_top = imagecreatefrompng('template.png');
	imagecopyresampled($img_src, $img_top, imagesx($img_src) - imagesx($img_top), imagesy($img_src) - imagesy($img_top), 0, 0, imagesx($img_top), imagesy($img_top), imagesx($img_top), imagesy($img_top));
	imageJPEG($img_src, $dst, $quality);
	imagedestroy($img_src);
	imagedestroy($img_top);
	return 0;
}
function doThumb($src, $dst, $dstWW=103, $dstHH=124, $quality = 95) {
    list($srcWW, $srcHH, $srcNM) = getimagesize($src);

    switch($srcNM) {
        case 1:
            $srcIMG = imagecreatefromgif($src);
            break;
        case 2:
            $srcIMG = imagecreatefromjpeg($src);
            break;
        case 3:
            $srcIMG = imagecreatefrompng($src);
            break;
    }

    $tmpWW = $dstWW;
    $tmpHH = number_format((($srcHH*$dstWW)/$srcWW), 0);

    if($tmpHH < $dstHH) {
        $tmpWW = number_format((($srcWW*$dstHH)/$srcHH), 0);
        $tmpHH = $dstHH;
    }

    $tmpIMG = imageCreateTrueColor($tmpWW, $tmpHH);
    //imageAntiAlias($tmpIMG, true); // kan användas om det finns.
    imagecopyresampled($tmpIMG, $srcIMG, 0, 0, 0, 0, $tmpWW, $tmpHH, $srcWW, $srcHH);
    if($tmpHH > $dstHH) {
        $dstYY = number_format(($tmpHH/2)-($dstHH/2), 0);
        $dstXX = 0;
    } else {
        $dstYY = 0;
        $dstXX = number_format(($tmpWW/2)-($dstWW/2), 0);
    }

    $dstIMG = imageCreateTrueColor($dstWW, $dstHH);
    imagecopyresampled($dstIMG, $tmpIMG, 0, 0, $dstXX, $dstYY, $dstWW, $dstHH, $dstWW, $dstHH);
    imageJpeg($dstIMG, $dst, $quality);
    imagedestroy($srcIMG);
    imagedestroy($tmpIMG);
	return 0;
}

?>