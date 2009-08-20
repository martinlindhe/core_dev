<?php

require_once('/var/www/core_dev/core/service_weather_webservicex.php');

$x = webservicex_weather('stockholm');

print_r($x);

?>
