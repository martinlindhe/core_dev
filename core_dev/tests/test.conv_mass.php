<?php

require_once('/var/www/core_dev/core/conv_mass.php');

$m = new mass();
$m->precision = 2;

if ($m->conv('kg', 'lb', 500) != 1102.31) echo "FAIL 1: ".$m->conv('kg', 'lb', 500)."\n";
if ($m->conv('lb', 'kg', 500) != 226.8)   echo "FAIL 2: ".$m->conv('lb', 'kg', 500)."\n";
if ($m->conv('t', 'kg', 1) != 1000) echo "FAIL 3\n";
if ($m->conv('kg', 't', 2000) != 2) echo "FAIL 4\n";

?>
