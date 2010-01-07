<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('client_weather_webservicex.php');

$client = new weather_webservicex();

$weather = $client->getWeather('stockholm', 'sweden');

print_r($weather);

?>
