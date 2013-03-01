<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageText.php');


$file = '/home/ml/Skrivbord/DSC_1853.JPG';
$font = '/home/ml/dev/data_fonts_DroidSerif-Bold.ttf';

// $font = '/home/ml/dev/Averia-Libre-Regular.woff';  //XXX dont work with woff files, 2012-05-16

$x = new ImageText($file);

$x->writeText('abc', $font);
$x->render('gif', 'texted.gif', array(), 30);
echo "wrote to ".$x->getWidth()."x".$x->getHeight()."\n";


?>
