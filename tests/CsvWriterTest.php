<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('CsvWriter.php');

class CsvTestRow {
    var $col1;
    var $col2;
    function __construct($c1, $c2) {
        $this->col1 = $c1;
        $this->col2 = $c2;
    }
}

class CsvWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $list = array();
        $list[] = new CsvTestRow("hej där", 123456.999100091);
        $list[] = new CsvTestRow("kalle", 200.00000000001);

        $writer = new CsvWriter();
        $writer->setData($list);

        $expected_csv =
        '"col1";"col2"'."\r\n".
        '"hej där";"123456.99910009"'."\r\n".
        '"kalle";"200.00000000001"'."\r\n";

        $this->assertEquals($writer->render(), $expected_csv);
    }
}
