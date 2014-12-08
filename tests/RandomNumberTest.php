<?php

namespace cd;

class RandomNumberTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $low = 10;
        $hi  = 20;
        $d = RandomNumber::getInRange($low, $hi);

        $this->assertGreaterThanOrEqual($low, $d);
        $this->assertLessThanOrEqual($hi, $d);
    }
}
