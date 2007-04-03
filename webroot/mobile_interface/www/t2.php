<?php
function copyRe($src, $dst, $dst2, $x, $y, $w, $h, $end='', $mime = '', $quality=91) {
	switch($end) {
		case 'jpg':
#			$srcIMG = @call_user_func('ImageCreateFromJPEG', $src);
			$srcIMG = imagecreatefromjpeg($src);
			break;
		case 'jpeg':
#			$srcIMG = @call_user_func('ImageCreateFromJPEG', $src);
			$srcIMG = imagecreatefromjpeg($src);
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
	return imageJpeg($dstIMG, '', $quality);
	#make_pres($dst, $dst, 150, 200);
	#make_pres($dst, $dst2, 75, 100, 89);
	#imagedestroy($srcIMG);
	#imagedestroy($dstIMG);
	#return true;
}
copyRe('test.jpg', 'test2.gif', 'test3.jpg', 60, 50, 100, 100, 'jpg');

?> 