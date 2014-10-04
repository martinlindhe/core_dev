<?php

namespace cd;

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
