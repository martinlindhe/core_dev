<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('sql_misc.php');

class sql_miscTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertEquals(sql_date(''), '');
    }

    public function test2()
    {
        $this->assertEquals(sql_time(''), '');
    }

    public function test3()
    {
        $this->assertEquals(sql_datetime(''), '');
    }

    public function test4()
    {
        // VERIFY fixed bug wont reappear: date "0000-00-00" got converted to "-0001-11-30"
        $this->assertEquals(sql_date('0000-00-00'), '0000-00-00');
    }
}
