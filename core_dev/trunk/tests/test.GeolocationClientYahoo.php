<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GeolocationClientYahoo.php');

$city = 'Stockholm';
$country = 'Sweden';

$loc_lookup = new GeolocationClientYahoo();
$x = $loc_lookup->get($city, $country);

d($x);

?>
