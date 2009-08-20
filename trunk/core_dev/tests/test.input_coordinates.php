<?php

require_once('/var/www/core_dev/core/input_coordinates.php');

if (gpsToWGS84('59 20 7.12N')      != 59.335311) echo "FAIL 1\n";
if (gpsToWGS84('59 20 7.12 N')     != 59.335311) echo "FAIL 2\n";
if (gpsToWGS84('N59 20 7.12')      != 59.335311) echo "FAIL 3\n";
if (gpsToWGS84('N 59 20 7.12')     != 59.335311) echo "FAIL 4\n";
if (gpsToWGS84('N 59° 20\' 7.12"') != 59.335311) echo "FAIL 5\n";
if (gpsToWGS84('59° 20\' 7.12" N') != 59.335311) echo "FAIL 6\n";
if (gpsToWGS84('N 59° 20.1187\'')  != 59.335312) echo "FAIL 7\n";	//XXX less precision

?>
