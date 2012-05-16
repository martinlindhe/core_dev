<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageResizer.php');


$file = '/home/ml/Skrivbord/DSC_1853.JPG';
$x = new ImageResizer($file);

$x->resizeAspect(800, 800);
$x->render('gif', 'resized.gif');
echo "resized to ".$x->getWidth()."x".$x->getHeight()."\n";


?>
