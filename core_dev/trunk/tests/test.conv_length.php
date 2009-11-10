<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('conv_length.php');

$l = new ConvertLength();
$l->setPrecision(2);

if ($l->conv('m', 'au', 100000000000) != 0.67) echo "FAIL1\n";
if ($l->conv('au', 'm', 2) != 299195742928) echo "FAIL2\n";

if ($l->conv('m', 'ft', 100) != 328.08) echo "FAIL3\n";
if ($l->conv('ft', 'm', 100) != 30.48) echo "FAIL4\n";

if ($l->conv('m', 'in', 100) != 3937.01) echo "FAIL5\n";
if ($l->conv('in', 'm', 100) != 2.54) echo "FAIL6\n";

if ($l->conv('m', 'yd', 1000) != 1093.61) echo "FAIL7\n";
if ($l->conv('yd', 'm', 1000) != 914.4) echo "FAIL8\n";

if ($l->conv('usmile','m', 1.5) != 2414.02) echo "FAIL9\n";
if ($l->conv('m','usmile', 1000) != 0.62) echo "FAIL10\n";

if ($l->conv('ukmile','m', 1.5) != 2778) echo "FAIL11\n";
if ($l->conv('m','ukmile', 300) != 0.16) echo "FAIL12\n";

if ($l->conv('mm', 'yd', 500) != 0.55) echo "FAIL13\n";
if ($l->conv('yd', 'mm', 0.25) != 228.6) echo "FAIL14\n";

if ($l->conv('mm', 'nm', 10) != 10000000) echo "FAIL15\n";
if ($l->conv('nm', 'mm', 500000) != 0.5) echo "FAIL16\n";

if ($l->conv('pm', 'nm', 500000) != 500) echo "FAIL17\n";
if ($l->conv('nm', 'pm', 0.04) != 40) echo "FAIL18\n";

?>
