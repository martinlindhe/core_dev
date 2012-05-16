<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('Image.php');


$x = new Image('/home/ml/Skrivbord/DSC_1853.JPG');
d($x);
$x->render('gif', 'xxx.gif');



?>
