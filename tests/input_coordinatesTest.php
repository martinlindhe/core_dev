<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('input_coordinates.php');

class input_coordinatesTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertEquals(gpsToWGS84('59 20 7.12N'), 59.335311);
        $this->assertEquals(gpsToWGS84('59 20 7.12 N'), 59.335311);
        $this->assertEquals(gpsToWGS84('N59 20 7.12'), 59.335311);
        $this->assertEquals(gpsToWGS84('N 59 20 7.12'), 59.335311);
        $this->assertEquals(gpsToWGS84('N 59° 20\' 7.12"'), 59.335311);
        $this->assertEquals(gpsToWGS84('59° 20\' 7.12" N'), 59.335311);
        $this->assertEquals(gpsToWGS84('N 59° 20.1187\''), 59.335312); // less precision
    }
}
