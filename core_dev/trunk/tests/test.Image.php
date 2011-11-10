<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageResizer.php');
require_once('ImageRotator.php');


/*
$x = new Image('/devel/web/fmf/pearl/cthulhu_eat.jpg'); // 300x115
$x->render('gif', 'xxx.gif');
*/

/*
$x = new ImageResizer('/devel/web/fmf/pearl/cthulhu_eat.jpg'); // 300x115

$x->resizeAspect(100, 50);
$x->render('gif', 'x50.gif');
echo "resized to ".$x->getWidth()."x".$x->getHeight()."\n";
*/


$x = new ImageRotator('/devel/cs/admin-ivr.unicorn.se/uploads/363');

$x->rotate(90);
$x->render('jpg', 'x90.jpg');

$x->rotate(90);
$x->render('jpg', 'x180.jpg');

$x->rotate(90);
$x->render('jpg', 'x270.jpg');

?>
