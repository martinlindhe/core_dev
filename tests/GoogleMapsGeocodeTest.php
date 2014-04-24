<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GoogleMapsGeocode.php');

class GoogleMapsGeocodetTest extends \PHPUnit_Framework_TestCase
{
    public function testGeocode1()
    {
        $x = GoogleMapsGeocode::geocode('Stora Nygatan, Stockholm, Sweden');
        $this->assertEquals($x->latitude, 59.3243643);
        $this->assertEquals($x->longitude, 18.0690275);
        $this->assertEquals($x->name, "Stora Nygatan, Södermalm, Stockholm, Sweden");
    }

    public function testGeocode2()
    {
        $x = GoogleMapsGeocode::geocode('gillerbacken');
        $this->assertEquals($x->latitude, 59.2542869);
        $this->assertEquals($x->longitude, 18.029657);
        $this->assertEquals($x->name, "Gillerbacken, 124 64 Bandhagen, Sweden");
    }

    public function testReverse1()
    {
        $x = GoogleMapsGeocode::reverse(59.3243643, 18.0690275);
        $this->assertEquals($x->description, 'Stora Nygatan 23, 111 27 Stockholm, Sweden');
    }
}
