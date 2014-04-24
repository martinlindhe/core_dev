<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertVolume.php');

class ConvertNumeralTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('kl',          'litres',    1,               1000),
        array('us gallon',   'liter',     2,               7.570823568),
        array('uk gallon',   'liter',     2,               9.09218),
        array('cubic meter', 'us gallon', 528,             139482.84364510236332058715 ),
        array('cubic meter', 'deciliter', 0.5,             5000),
        array('deciliter',   'liter',     5,               0.5),
        array('milliliter',  'us gallon', 3.785411784 * 2, 0.002),
        );

        foreach ($arr as $test)
        {
            $res = ConvertVolume::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
