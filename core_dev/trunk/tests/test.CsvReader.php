<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('CsvReader.php');

$row = '"AAPL",357.05,357.01,194.06,360.00';

$x = CsvReader::parseRow($row);

var_dump( $x );

?>
