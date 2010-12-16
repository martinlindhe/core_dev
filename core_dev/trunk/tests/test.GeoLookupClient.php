<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GeoLookupClient.php');

$c = new GeoLookupClient(59.332169, 18.062429); //= sthlm

$x = $c->get();
var_dump( $x );

?>
