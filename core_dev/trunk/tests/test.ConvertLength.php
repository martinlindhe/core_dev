<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertLength.php');

$l = new ConvertLength();
$l->setPrecision(2);

if ($l->conv('m', 'au', 100000000000) != 0.67) echo "FAIL 1\n";
if ($l->conv('au', 'm', 2) != 299195741400)    echo "FAIL 2\n";

if ($l->conv('m', 'ft', 100) != 328.08)        echo "FAIL 3\n";
if ($l->conv('ft', 'm', 100) != 30.48)         echo "FAIL 4\n";

if ($l->conv('m', 'in', 100) != 3937.01)       echo "FAIL 5\n";
if ($l->conv('in', 'm', 100) != 2.54)          echo "FAIL 6\n";

if ($l->conv('m', 'yd', 1000) != 1093.61)      echo "FAIL 7\n";
if ($l->conv('yd', 'm', 1000) != 914.4)        echo "FAIL 8\n";

if ($l->conv('usmile','m', 1.5) != 2414.02)    echo "FAIL 9\n";
if ($l->conv('m','usmile', 1000) != 0.62)      echo "FAIL 10\n";

if ($l->conv('ukmile','m', 1.5) != 2778)       echo "FAIL 11\n";
if ($l->conv('m','ukmile', 300) != 0.16)       echo "FAIL 12\n";

if ($l->conv('mm', 'yd', 500) != 0.55)         echo "FAIL 13\n";
if ($l->conv('yd', 'mm', 0.25) != 228.6)       echo "FAIL 14\n";

if ($l->conv('mm', 'nm', 10) != 10000000)      echo "FAIL 15\n";
if ($l->conv('nm', 'mm', 500000) != 0.5)       echo "FAIL 16\n";

if ($l->conv('pm', 'nm', 500000) != 500)       echo "FAIL 17\n";
if ($l->conv('nm', 'pm', 0.04) != 40)          echo "FAIL 18\n";

if ($l->convLiteral('1 meter', 'cm') != 100)       echo "FAIL 19\n";
if ($l->convLiteral('10 mil', 'kilometer') != 100) echo "FAIL 20\n";
if ($l->convLiteral('1 micrometer', 'nanometer') != 1000) echo "FAIL 21\n";

?>
