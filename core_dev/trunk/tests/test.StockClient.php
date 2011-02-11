<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('StockClient.php');

$stock = StockClient::getNasdaq('AAPL'); //Apple

if ($stock->name != 'Apple Inc.') {
    echo "FAIL 1\n";
    print_r($stock);
}

?>
