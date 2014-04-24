<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('DiceDnD.php');

class DiceDnDTest extends \PHPUnit_Framework_TestCase
{
    private function assertRoll($d)
    {
        $this->assertGreaterThanOrEqual($d->min(), $d->roll());
        $this->assertLessThanOrEqual($d->max(), $d->roll());
    }

    public function testRoll1()
    {
        $d = new DiceDnD('2D6');
        $this->assertEquals($d->min(), 2);
        $this->assertEquals($d->max(), 12);
        $this->assertRoll($d);
    }

    public function testRoll2()
    {
        $d = new DiceDnD('2D6+2');
        $this->assertEquals($d->min(), 4);
        $this->assertEquals($d->max(), 14);
        $this->assertRoll($d);
    }

    public function testRoll3()
    {
        $d = new DiceDnD('3D6-2');
        $this->assertEquals($d->min(), 1);
        $this->assertEquals($d->max(), 16);
        $this->assertRoll($d);
    }

    public function testRoll4()
    {
        $d = new DiceDnD('1D8+60');
        $this->assertEquals($d->min(), 61);
        $this->assertEquals($d->max(), 68);
        $this->assertRoll($d);
    }

}
