<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Timestamp.php');

class TimestampTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $t = new Timestamp('2012-06-02');

        $this->assertEquals($t->getSqlDate(), "2012-06-02");
    }
}
