<?php

namespace cd;

class YahooQueryClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $city = 'Los Angeles ';
        $country = 'USA';

        $x = YahooQueryClient::geocode($city, $country);

        // d($x);

        $this->assertEquals($x->name, "Los Angeles");
        $this->assertEquals($x->country, "US");
    }
}
