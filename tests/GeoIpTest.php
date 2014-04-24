<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GeoIp.php');

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
