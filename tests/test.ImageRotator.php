<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageRotator.php');


$file = '/home/ml/Skrivbord/DSC_1853.JPG';
$x = new ImageRotator($file);

$x->rotate(90);
$x->render('jpg', 'rotated90.jpg');

$x->rotate(90);
$x->render('jpg', 'rotated180.jpg');

$x->rotate(90);
$x->render('jpg', 'rotated270.jpg');

?>
