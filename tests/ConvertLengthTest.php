<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertLength.php');

class ConvertLengthTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('m',          'au',        100000000000, 0.66845871222684454959),
        array('au',         'm',         2,        299195741400),
        array('m',          'ft',        100,      328.08333290409097278381),
        array('ft',         'm',         100,      30.480061),
        array('m',          'in',        100,      3937.00787401574803149606),
        array('in',         'm',         100,      2.54),
        array('m',          'yd',        1000,     1093.61329833770778652668),
        array('yd',         'm',         1000,     914.4),
        array('usmile',     'm',         1.5,      2414.016),
        array('m',          'usmile',    1000,     0.62137119223733396961),
        array('ukmile',     'm',         1.5,      2778),
        array('m',          'ukmile',    300,      0.16198704103671706263),
        array('mm',         'yd',        500,      0.54680664916885389326),
        array('yd',         'mm',        0.25,     228.6),
        array('mm',         'nm',        10,       10000000),
        array('nm',         'mm',        500000,   0.5),
        array('pm',         'nm',        500000,   500),
        array('nm',         'pm',        0.04,     40),
        array('fm',         'pm',        50000000, 50000),
        array('am',         'fm',        50000000, 50000),
        array('zm',         'am',        50000000, 50000),
        array('ym',         'zm',        50000000, 50000),
        array('km',         'ld',        500000,   1.30072840790842872008),
        array('meter',      'cm',        1,        100),
        array('mil',        'kilometer', 10,       100),
        array('micrometer', 'nanometer', 1,        1000),
        );

        foreach ($arr as $test)
        {
            $res = ConvertLength::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
