<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('YahooQueryClient.php');

$city = 'Los Angeles ';
$country = 'USA';

$x = YahooQueryClient::geocode($city, $country);

d($x);

?>
