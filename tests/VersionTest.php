<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Version.php');

class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $x = new Version('4.0.32.233');
        $this->assertEquals($x->get(), '4.0.32.233');
    }

    public function test2()
    {
        $x = new Version('r123');
        $this->assertEquals($x->get(), 'r123');
    }
}
