<?php

namespace cd;

class ConvertMassTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('kg', 'lb',   500,  1102.31131092438790361486),
        array('lb', 'kg',   500,  226.796185),
        array('t',  'kg',   1,    1000),
        array('kg', 't',    2000, 2),
        array('oz', 'g',    1,    28.349523125),
        array('kg', 'gram', 1,    1000),
        );

        foreach ($arr as $test)
        {
            $res = ConvertMass::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
