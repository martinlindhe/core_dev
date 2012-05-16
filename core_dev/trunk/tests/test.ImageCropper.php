<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageCropper.php');


$file = '/home/ml/Skrivbord/DSC_1853.JPG';
$x = new ImageCropper($file);
$x->crop(500, 500, 1200, 1200);

$x->render('png', 'cropped.png');

?>
