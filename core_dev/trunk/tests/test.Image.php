<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('ImageResizer.php');


/*
$x = new Image('/devel/web/fmf/pearl/cthulhu_eat.jpg'); // 300x115
$x->write('gif', 'xxx.gif');
*/

$x = new ImageResizer('/devel/web/fmf/pearl/cthulhu_eat.jpg'); // 300x115

$x->resizeAspect(100, 50);
$x->write('gif', 'x50.gif');
echo "resized to ".$x->getWidth()."x".$x->getHeight()."\n";



?>
