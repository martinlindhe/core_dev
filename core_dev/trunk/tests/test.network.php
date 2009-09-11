<?php
require_once('/var/www/core_dev/core/network.php');

$x = IPv4_to_GeoIP('192.168.0.1');
$valid = array(
	'192.168.0.1',
	'80.0.0.0/8',
	'240.0.0.0/4'
);

if ($x != 3232235521) echo "FAIL 1\n";
if (GeoIP_to_IPv4($x) != '192.168.0.1') echo "FAIL 2\n";
if (matchIP('240.212.11.42', $valid)) echo "FAIL 3\n";
if (!reservedIP('192.168.0.100')) echo "FAIL 4\n";
if (!reservedIP('10.20.30.40')) echo "FAIL 5\n";

?>
