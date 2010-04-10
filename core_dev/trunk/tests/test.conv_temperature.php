<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('conv_temperature.php');

$t = new ConvertTemperature();
$t->setPrecision(2);

if ($t->conv('C', 'F', 300) != 572)       echo "FAIL 1\n";
if ($t->conv('C', 'K', 300) != 573.15)    echo "FAIL 2\n";
if ($t->conv('C', 'R', 300) != 1031.67)   echo "FAIL 3\n";

if ($t->conv('F', 'C', 500) != 260)       echo "FAIL 4\n";
if ($t->conv('F', 'K', 500) != 533.15)    echo "FAIL 5: ".$t->conv('F', 'K', 500)."\n";
if ($t->conv('F', 'R', 500) != 959.67)    echo "FAIL 6\n";

if ($t->conv('K', 'C', 0) != -273.15)     echo "FAIL 7\n";
if ($t->conv('K', 'F', 0) != -459.67)     echo "FAIL 8: ".$t->conv('K', 'F', 0)."\n";
if ($t->conv('K', 'R', 0) != 0)           echo "FAIL 9\n";

if ($t->conv('R', 'C', 509.67) != 10)     echo "FAIL 10\n";
if ($t->conv('R', 'F', 509.67) != 50)     echo "FAIL 11\n";
if ($t->conv('R', 'K', 509.67) != 283.15) echo "FAIL 12\n";

?>
