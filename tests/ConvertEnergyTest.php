<?php

namespace cd;

class ConvertEnergyTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('kWh', 'MWh', 15, 0.015),
        array('GWh', 'kWh', 15, 15000000),
        );

        foreach ($arr as $test)
        {
            $res = ConvertEnergy::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
