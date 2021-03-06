<?php

namespace cd;

class IsbnTest extends \PHPUnit_Framework_TestCase
{
    function test1()
    {
        $this->assertEquals(ISBN::isValid('978-0-552-77429-1'), true);
    }

    function test2()
    {
        $this->assertEquals(ISBN::isValid('978-91-7429-121-6'), true);
    }

    function test3()
    {
        // NOTE: has bad checksum
        $this->assertEquals(ISBN::isValid('978-91-7429-121-1'), false);
    }
}
