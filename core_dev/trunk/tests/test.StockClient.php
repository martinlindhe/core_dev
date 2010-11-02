<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('StockClient.php');

$quote = new StockClient();
$x = $quote->getNasdaq('AAPL'); //Apple
if ($x['Name'] != 'Apple Inc.') {
    echo "FAIL 1\n";
    print_r($x);
}

?>
