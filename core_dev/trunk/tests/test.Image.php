<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('Image.php');

//xxx. resize / scale

$x = new Image('/devel/web/fmf/pearl/cthulhu_eat.jpg'); // 300x115

$x->write('gif', 'xxx.gif');

?>
