<?php

namespace cd;

class StockClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $stock = StockClient::getNasdaq('AAPL'); //Apple

        $this->assertEquals($stock->name, 'Apple Inc.');
        $this->assertEquals($stock->symbol, 'AAPL');
    }

}
