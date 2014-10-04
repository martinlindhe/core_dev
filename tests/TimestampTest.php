<?php

namespace cd;

class TimestampTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $t = new Timestamp('2012-06-02');

        $this->assertEquals($t->getSqlDate(), "2012-06-02");
    }
}
