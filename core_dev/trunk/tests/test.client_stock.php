<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('client_stock.php');

$quote = new Stock();
$x = $quote->getNasdaq('AAPL'); //Apple
if ($x['Name'] != 'Apple Inc.') {
	echo "FAIL 1\n";
	print_r($x);
}

?>
