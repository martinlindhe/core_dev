<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GeolocationClientYahoo.php');

$city = 'Los Angeles ';
$country = 'USA';

$loc_lookup = new GeolocationClientYahoo();
$x = $loc_lookup->get($city, $country);

d($x);

?>
