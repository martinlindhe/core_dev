<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertArea.php');

class ConvertAreaTest extends \PHPUnit_Framework_TestCase
{
    public function testConversions()
    {

        $arr = array(
        array('m²', 'are',                        1,      0.01),
        array('ha', 'square metre',               2,      20000),
        array('square kilometer', 'square meter', 2,      2000000),
        array('acres', 'square meter',            3,      12140.5692672),
        array('square feet', 'square meter',      2,      0.18580608),
        array('square yard', 'square meter',      4,      3.34450944),
        array('acre', 'hectare',                  140,    56.6559899136),
        array('acre', 'hectare',                  1,      0.40468564224),
        array('cm²', 'm²',                        100000, 10),
        array('mm²', 'cm²',                       100000, 1000),
        array('dm²', 'cm²',                       100,    10000),
        array('square inches', 'cm²',             100,    645.16),
        array('square foot', 'square inch',       1,      144),
        );

        foreach ($arr as $test)
        {
            $res = ConvertArea::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
