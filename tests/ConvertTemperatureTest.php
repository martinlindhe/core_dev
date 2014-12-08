<?php

namespace cd;

class ConvertTemperatureTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('C', 'F',      300,     572),
        array('C', 'K',      300,     573.15),
        array('C', 'R',      300,     1031.67),
        array('F', 'C',      500,     260),
        array('F', 'K',      500,     533.15),
        array('F', 'R',      500,     959.67),
        array('K', 'C',      0,      -273.15),
        array('K', 'F',      0,      -459.67),
        array('K', 'R',      0,       0),
        array('R', 'C',      509.67,  10),
        array('R', 'F',      509.67,  50),
        array('R', 'K',      509.67,  283.15),
        array('C', 'kelvin', 100,     373.15),
        );

        foreach ($arr as $test)
        {
            $res = ConvertTemperature::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
