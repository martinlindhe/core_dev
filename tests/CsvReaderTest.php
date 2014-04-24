<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('CsvReader.php');

class CsvReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testParseRow()
    {
        $row = '"AAPL",357.05,357.01,194.06,360.00';

        $parsed = CsvReader::parseRow($row);

        $this->assertEquals(count($parsed), 5);
        $this->assertEquals($parsed[0], "AAPL");
        $this->assertEquals($parsed[1], "357.05");
        $this->assertEquals($parsed[2], "357.01");
        $this->assertEquals($parsed[3], "194.06");
        $this->assertEquals($parsed[4], "360.00");
    }
}
