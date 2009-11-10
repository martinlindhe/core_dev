<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('conv_mass.php');

$m = new ConvertMass();
$m->setPrecision(2);

if ($m->conv('kg', 'lb', 500) != 1102.31) echo "FAIL 1: ".$m->conv('kg', 'lb', 500)."\n";
if ($m->conv('lb', 'kg', 500) != 226.8)   echo "FAIL 2: ".$m->conv('lb', 'kg', 500)."\n";
if ($m->conv('t', 'kg', 1) != 1000) echo "FAIL 3\n";
if ($m->conv('kg', 't', 2000) != 2) echo "FAIL 4\n";
if ($m->conv('oz', 'g', 1) != 28.35) echo "FAIL 5\n";

?>
