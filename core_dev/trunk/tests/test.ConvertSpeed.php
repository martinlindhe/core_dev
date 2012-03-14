<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertSpeed.php');

$m = new ConvertSpeed();
$m->setPrecision(2);

if ( $m->conv('mph', 'km/h', 18) != 28.97) echo "FAIL 1\n";
if ( $m->conv('km/h', 'm/s', 18) != 5)     echo "FAIL 2\n";
if ( $m->conv('ft/s', 'mph', 12) != 8.18)  echo "FAIL 3\n";
if ( $m->conv('knot', 'm/s', 20) != 10.29) echo "FAIL 4\n";

?>
