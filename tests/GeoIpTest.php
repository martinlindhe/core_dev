<?php

namespace cd;

class GeoIpTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCountry()
    {
        $this->assertEquals( GeoIp::getCountry('www.ica.se'), 'SE');
    }

    public function testGetTimezone()
    {
        $this->assertEquals( GeoIp::getTimezone('www.stockholm.se'), 'Europe/Stockholm');
    }

    public function testGetRecord1()
    {
        $res = GeoIp::getRecord('www.google.com');

        // NOTE this may not always hold true
        $this->assertEquals($res['country_name'], 'United States');
        $this->assertEquals($res['region'], 'CA');
        $this->assertEquals($res['city'], 'Mountain View');
    }

}
