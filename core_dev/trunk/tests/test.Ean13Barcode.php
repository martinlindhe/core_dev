<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('BarcodeEan13.php');
require_once('core.php');

$x = new BarcodeEan13('7 310070 030603');


echo $x->render()."\n";

?>
