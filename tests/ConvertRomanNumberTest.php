<?php

namespace cd;

class ConvertRomanNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid1()
    {
        $this->assertEquals(ConvertRomanNumber::isValid('XII'), true);
    }

    public function testIsValid2()
    {
        $this->assertEquals(ConvertRomanNumber::isValid('MMC1'), false);
    }

    public function testConvert1()
    {
        $this->assertEquals(ConvertRomanNumber::convert('roman', 'decimal', 'MCMLXXXVIII'), 1988);
    }

    public function testConvert2()
    {
        $x = new ConvertRomanNumber('XIV');
        $this->assertEquals($x->getAsRoman(), 'XIV');
    }

    public function testConvert3()
    {
        $x = new ConvertRomanNumber('MMX');
        $this->assertEquals($x->getAsInteger(), 2010);
    }

    public function testConvert4()
    {
        $x = new ConvertRomanNumber('MCMXCIX');
        $this->assertEquals($x->getAsInteger(), 1999);
    }

    public function testConvert5()
    {
        $x = new ConvertRomanNumber(1988);
        $this->assertEquals($x->getAsRoman(), 'MCMLXXXVIII');
    }

    public function testConvert6()
    {
        $x = new ConvertRomanNumber('MMMMCMXCIX');
        $this->assertEquals($x->getAsInteger(), 4999);
    }
}
