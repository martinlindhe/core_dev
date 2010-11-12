<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertVolume.php');

$m = new ConvertVolume();
$m->setPrecision(2);

if ($m->convLiteral('1 m³', 'litres') != 1000)       echo "FAIL 1\n";
if ($m->convLiteral('2 us_gallon', 'liter') != 7.57) echo "FAIL 2\n";
if ($m->convLiteral('2 uk_gallon', 'liter') != 9.09) echo "FAIL 3\n";

?>
