<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('StockClient.php');

class StockClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $stock = StockClient::getNasdaq('AAPL'); //Apple

        $this->assertEquals($stock->name, 'Apple Inc.');
        $this->assertEquals($stock->symbol, 'AAPL');
    }

}
