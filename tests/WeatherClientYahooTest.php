<?php

namespace cd;

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
