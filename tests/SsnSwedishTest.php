<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SsnSwedish.php');
require_once('core.php');

class SsnSwedishTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertEquals(SsnSwedish::isValid(  '811218-9876'), true);  // known correct
        $this->assertEquals(SsnSwedish::isValid('19811218-9876', SsnSwedish::MALE), true);  // known to be a male ssn
        $this->assertEquals(SsnSwedish::isValid('19811218-9876'), true);

        $this->assertEquals(SsnSwedish::isValid(  '800222-2222'), false); // known fake
        $this->assertEquals(SsnSwedish::isValid('20811218-9876'), false);  // BAD, in the future!



        $this->assertEquals( sql_date( SsnSwedish::getTimestamp('811218-9876') ), '1981-12-18');

        $this->assertEquals(SsnSwedish::getGender('811218-9876'), 'M');
    }

    public function test2()
    {
        $this->assertEquals(OrgNoSwedish::isValid('556455-4656'), true); // Unicorn Communications AB
        $this->assertEquals(OrgNoSwedish::isValid('556632-0221'), true); // Unicorn Interactive AB
        $this->assertEquals(OrgNoSwedish::isValid('916629-8738'), true); // UNICORN DESIGN HANDELSBOLAG
        $this->assertEquals(OrgNoSwedish::isValid('969722-7743'), true); // Royal Unicorn Handelsbolag
    }
}
