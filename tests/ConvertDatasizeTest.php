<?php

namespace cd;

class ConvertDatasizeTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $arr = array(
        array('megabyte',  'byte',     0.5,  524288),
        array('megabit',   'megabyte', 100,  12.5),
        array('GB',        'MiB',      1,    1024),
        array('zb',        'tb',       1,    1073741824),
        array('zettabyte', 'exabyte',  1,    1024),
        array('zettabit',  'exabit',   1,    1024),
        );

        foreach ($arr as $test)
        {
            $res = ConvertDatasize::convert($test[0], $test[1], $test[2]);
            $this->assertEquals($res, $test[3]);
        }
    }
}
