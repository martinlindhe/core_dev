<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('BitManipulation.php');

class BitManipulationTest extends \PHPUnit_Framework_TestCase
{
    public function testSplitByte1()
    {
        $this->assertEquals(BitManipulation::SplitByte(0xfa), array(0xf, 0xa));
    }

    public function testSplitByte2()
    {
        $this->assertEquals(BitManipulation::SplitByte(0xff), array(0xf, 0xf));
    }

    public function testSplitByte3()
    {
        // "D" = 0x44
        $this->assertEquals(BitManipulation::SplitByte('D'), array(4, 4));
    }

    public function testSplitByte4()
    {
        $this->assertEquals(BitManipulation::SplitByte("\xca"), array(0xc, 0xa));
    }

    public function testByteToBits1()
    {
        $this->assertEquals(BitManipulation::ByteToBits(0x01), array(7=>0,6=>0,5=>0,4=>0,3=>0,2=>0,1=>0,0=>1));
    }

    public function testByteToBits2()
    {
        $this->assertEquals(BitManipulation::ByteToBits(0xff), array(7=>1,6=>1,5=>1,4=>1,3=>1,2=>1,1=>1,0=>1));
    }

    public function testByteToBits3()
    {
        $this->assertEquals(BitManipulation::ByteToBits(0x80), array(7=>1,6=>0,5=>0,4=>0,3=>0,2=>0,1=>0,0=>0));
    }
}
