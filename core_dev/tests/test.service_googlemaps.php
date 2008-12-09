<?php
require_once('/var/www/core_dev/core/service_googlemaps.php');

$path[0]['x'] = gpsToWGS84('62 23 37.00N');
$path[0]['y'] = gpsToWGS84('017 18 28.00E');

$path[1]['x'] = gpsToWGS84('62 23 37.10N');
$path[1]['y'] = gpsToWGS84('017 18 35.50E');

$path[2]['x'] = gpsToWGS84('62 23 34.10N');
$path[2]['y'] = gpsToWGS84('017 18 25.50E');

$path[3]['x'] = gpsToWGS84('62 23 32.10N');
$path[3]['y'] = gpsToWGS84('017 18 38.50E');

$path[4]['x'] = gpsToWGS84('62 23 42.10N');
$path[4]['y'] = gpsToWGS84('017 18 50.50E');
echo xhtmlImage( googleMapsStaticMap($path[0]['x'], $path[0]['y'], $path, $path, 512, 512, 15) );






$pos[] = googleMapsGeocode('gillerbacken');
if ($pos[0]) echo xhtmlImage( googleMapsStaticMap($pos[0]['x'], $pos[0]['y'], $pos) );




$pos[0]['x'] = gpsToWGS84('59 20 7.12N');
$pos[0]['y'] = gpsToWGS84('18 04 9.61E');
echo xhtmlImage( googleMapsStaticMap($pos[0]['x'], $pos[0]['y'], $pos) );





$x = googleMapsReverseGeocode(gpsToWGS84('59 20 7.12N'), gpsToWGS84('18 04 9.61E'));
d($x);



?>
