<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertEnergy.php');

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
