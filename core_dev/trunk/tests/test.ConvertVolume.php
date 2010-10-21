<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertVolume.php');

$m = new ConvertVolume();
$m->setPrecision(2);

if ($m->convLiteral('1 m³', 'litres') != 1000) echo "FAIL 1\n";

?>
