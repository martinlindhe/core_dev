<?php

namespace cd;

class DurationTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $dur = new Duration(3600 + 46);
        $this->assertEquals($dur->render(), '1:00:46');
    }

    public function test2()
    {
        $dur = new Duration(64800 + 3180 + 19);
        $this->assertEquals($dur->render(), '18:53:19');
    }

    public function test3()
    {
        $dur = new Duration(5278156);
        $this->assertEquals($dur->render(), '1466:09:16');
    }

    public function test4()
    {
        $dur = new Duration('60s');
        $this->assertEquals($dur->renderRelative(), '1 minute');
    }

    public function test5()
    {
        $dur = new Duration('4w');
        $this->assertEquals($dur->renderRelative(), '4 weeks');
    }
}
