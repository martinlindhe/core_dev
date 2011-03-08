<?php
/**
 * $Id$
 */

die;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../../core/');

require_once('core.php');


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

$out = imagecreatetruecolor($use_width, $use_height * count($sprites) );


$idx = 0;
foreach ($sprites as $f) {
    $tmp = imagecreatefrompng($f);

    imagecopy($out, $tmp, 0, $idx * 11, 0, 0, imagesx($tmp), imagesy($tmp) );
    $idx++;
}

imagepng($out, 'sprite.png');

?>
