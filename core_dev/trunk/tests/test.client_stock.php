<?php

require_once('/var/www/core_dev/core/client_stock.php');

$quote = new Stock();
$x = $quote->getNasdaq('AAPL'); //Apple

print_r($x);

?>
