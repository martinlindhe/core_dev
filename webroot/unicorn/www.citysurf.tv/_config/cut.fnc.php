<?
function copyRe($src, $dst, $dst2, $x, $y, $w, $h, $end='', $mime = '', $quality=91) {
	switch($end) {
		case 'jpg':
#			$srcIMG = @call_user_func('ImageCreateFromJPEG', $src);
			$srcIMG = @imagecreatefromjpeg($src);
			break;
		case 'jpeg':
#			$srcIMG = @call_user_func('ImageCreateFromJPEG', $src);
			$srcIMG = @imagecreatefromjpeg($src);
			break;
		case 'png':
			$srcIMG = @imagecreatefrompng($src);
			break;
		case 'gif':
			$srcIMG = @imagecreatefromgif($src);
			break;
	}
	if(!$srcIMG) return false;
	$dstIMG = imageCreateTrueColor($w, $h);
	imagecopyresampled($dstIMG, $srcIMG, 0, 0, $x, $y, $w, $h, $w, $h);
	imageJpeg($dstIMG, $dst, $quality);
#header("Content-Type: image/jpeg");
#return imageJpeg($dstIMG, '', $quality);
	$sizes = explode('x', UIMG);
	make_pres($dst, $dst, $sizes[0], $sizes[1]);
	make_pres($dst, $dst2, 50, 50, 89);
	imagedestroy($srcIMG);
	imagedestroy($dstIMG);
	return true;
}
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
	if($info[0] >= $info[1] && $info[0] >= $dstWW) {
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
function doThumb($src, $dst, $dstWW=75, $dstHH=100, $quality = 91) {
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
    imagedestroy($dstIMG);
    imagedestroy($tmpIMG);
    imagedestroy($srcIMG);
	return 0;
}

function make_pres($src, $dst, $dstWW=75, $dstHH=100, $quality = 91) {
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
    imagedestroy($dstIMG);
    imagedestroy($tmpIMG);
    imagedestroy($srcIMG);
	return 0;
}

function verify_uploaded_file($str, $size) {

	$types = array("jpg", "jpeg", "gif", "png");
	$sizes['max'] = 1500000;
	$sizes['min'] = 0;

	if ($size < $sizes['min'] || $size > $sizes['max']) {
		return false; 
	}

	$arr = split('[.]', $str);
	$ext = strtolower($arr[count($arr) - 1]);

	if(!in_array($ext, $types)) {
		return false;
	} 
    
	return true;
}
?>