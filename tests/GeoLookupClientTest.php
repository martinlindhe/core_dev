<?php

namespace cd;

class GeoLookupClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        // TODO make proper test
        $res = GeoLookupClient::get(59.332169, 18.062429); //= sthlm

        $this->assertInstanceOf('cd\GeoLookupResult', $res);
    }
}
