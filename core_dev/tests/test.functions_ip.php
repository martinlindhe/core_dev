<?php
require_once('/home/ml/dev/core_dev/core/functions_ip.php');

$x = IPv4_to_GeoIP('192.168.0.1');
if ($x != 3232235521) echo "FAIL 1\n";

if (GeoIP_to_IPv4($x) != '192.168.0.1') echo "FAIL 2\n";

$valid = array(
	'192.168.0.1',
	'80.0.0.0/8',
	'240.0.0.0/4'
);

if (!allowedIPv4('240.212.11.42', $valid)) {
	echo "FAIL 3\n";
}

if (!reservedIPv4('192.168.0.100')) echo "FAIL 4\n";
if (!reservedIPv4('10.20.30.40')) echo "FAIL 5\n";

?>
