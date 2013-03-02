<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertArea.php');

$m = new ConvertArea();
$m->setPrecision(2);

if ($m->convLiteral('1 m²', 'are') != 0.01)                           echo "FAIL 1\n";
if ($m->convLiteral('2 ha', 'square metre') != 20000)                 echo "FAIL 2\n";
if ($m->convLiteral('2 square kilometer', 'square meter') != 2000000) echo "FAIL 3\n";
if ($m->convLiteral('3 acres', 'square meter') != 12140.57)           echo "FAIL 4\n";
if ($m->convLiteral('2 square feet', 'square meter') != 0.19)         echo "FAIL 5\n";
if ($m->convLiteral('4 square yard', 'square meter') != 3.34)         echo "FAIL 6\n";

if ($m->convLiteral('140 acre', 'hectare') != 56.66)                  echo "FAIL 7\n";

if ($m->conv('acre', 'hectare', 1) != 0.4)                            echo "FAIL 8\n";
if ($m->conv('cm²', 'm²', 100000) != 10)                              echo "FAIL 9\n";
if ($m->conv('mm²', 'cm²', 100000) != 1000)                           echo "FAIL 10\n";
if ($m->conv('dm²', 'cm²', 100) != 10000)                             echo "FAIL 10\n";

if ($m->conv('square inches', 'cm²', 100) != 645.16)                  echo "FAIL 11\n";


//echo ConvertArea::conv('acre', 'hectare', 1);


?>
