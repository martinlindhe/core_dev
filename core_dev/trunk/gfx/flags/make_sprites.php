<?php
/**
 * $Id$
 */

die;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../../core/');

require_once('core.php');


$out_sprite = 'sprite.png';
if (file_exists($out_sprite))
    unlink($out_sprite);

$sprites = dir_get_matches('./', array('*.png') );

$use_width = 16;
$use_height = 11;
foreach ($sprites as $f) {
    list($width, $height) = getimagesize($f);

    if ($use_width && $width > $use_width)
        throw new Exception ('wrong width: '.$width.' in '.$f);

    if ($height != $use_height)
        throw new Exception ('wrong height: '.$height.' in '.$f);
}

$im = imagecreatetruecolor($use_width, $use_height * count($sprites) );

//XXXX how to enable transparent pixel without using full alpha channel of png? this adds 3K of size to test png
imagesavealpha($im, true);
imagealphablending($im, false);

// make background transparent
$back = imagecolorallocatealpha($im, 255, 255, 255, 127);
imagefilledrectangle($im, 0, 0, imagesx($im) - 1, imagesy($im)- 1, $back);


$idx = 0;
foreach ($sprites as $f) {
    $tmp = imagecreatefrompng($f);

    imagecopy($im, $tmp, 0, $idx * 11, 0, 0, imagesx($tmp), imagesy($tmp) );
    $idx++;
}

imagepng($im, $out_sprite);

?>
