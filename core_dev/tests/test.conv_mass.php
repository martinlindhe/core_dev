<?php

require_once('/var/www/core_dev/core/conv_mass.php');

$m = new mass();

if ($m->conv('kg', 'lb', 500) != 1102.31131092) echo "FAIL 1\n";
if ($m->conv('lb', 'kg', 500) != 226.796185)    echo "FAIL 2\n";

?>
