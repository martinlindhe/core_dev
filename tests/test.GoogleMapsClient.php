<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GoogleMapsClient.php');
require_once('input_coordinates.php');

$x = GoogleMapsClient::geocode('Stora Nygatan, Stockholm, Sweden');
var_dump($x);



$pos = GoogleMapsClient::geocode('gillerbacken');
//d($pos);
echo GoogleMapsClient::staticMap($pos->latitude, $pos->longitude);




/*
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


echo GoogleMapsClient::staticMap($path[0]['x'], $path[0]['y'], $path, $path, 512, 512, 15);
*/


?>
