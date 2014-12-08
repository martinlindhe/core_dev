<?php

namespace cd;

class EpisodeTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $e = new Episode('season 1, episode 24');
        $this->assertEquals($e->get(), '1x24');
    }

    public function test2()
    {
        $e = new Episode('01x24');
        $this->assertEquals($e->get(), '1x24');
    }

    public function test3()
    {
        $e = new Episode('S01E24');
        $this->assertEquals($e->get(), '1x24');
    }

    public function test4()
    {
        $e = new Episode('s1e24');
        $this->assertEquals($e->get(), '1x24');
    }
}
