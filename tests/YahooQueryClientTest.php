<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('YahooQueryClient.php');

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
