<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('BarcodeEan13.php');
require_once('core.php');

$x = new BarcodeEan13('7310070030603');
if (!$x->isValid()) echo "FAIL 1\n";


$x = new BarcodeEan13('7310500078045');
if (!$x->isValid()) echo "FAIL 2\n";



echo $x->render()."\n";

?>
