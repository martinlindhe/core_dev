<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertVolume.php');

$m = new ConvertVolume();
$m->setPrecision(2);

if ($m->convLiteral('1 m³', 'litres') != 1000)            echo "FAIL 1\n";
if ($m->convLiteral('2 us gallon', 'liter') != 7.57)      echo "FAIL 2\n";
if ($m->convLiteral('2 uk gallon', 'liter') != 9.09)      echo "FAIL 3\n";
if ($m->convLiteral('3 cubic meter', 'gallon') != 792.52) echo "FAIL 4\n";

?>
