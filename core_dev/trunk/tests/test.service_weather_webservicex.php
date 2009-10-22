<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_weather_webservicex.php');

$x = webservicex_weather('stockholm');

print_r($x);

?>
