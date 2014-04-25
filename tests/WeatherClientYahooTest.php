<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('WeatherClientYahoo.php');

class WeatherClientYahooTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $client = new WeatherClientYahoo();

        $res = $client->getWeather('Uppsala', 'Sweden');

        $this->assertInstanceOf('cd\WeatherResult', $res);

        $this->assertEquals($res->city, "Uppsala");
    }
}
