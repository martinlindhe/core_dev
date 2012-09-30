<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertNumeral.php');

$d = new ConvertNumeral();

if ($d->conv('decimal', 'binary', '11') != '1011')   echo "FAIL 1\n";
if ($d->conv('decimal', 'octal', '44')  != '54')     echo "FAIL 2\n";
if ($d->conv('octal', 'decimal', '33')  != '27')     echo "FAIL 3\n";
if ($d->conv('octal', 'decimal', '1234') != '668')   echo "FAIL 4\n";
if ($d->conv('binary', 'decimal', '1110') != '14')   echo "FAIL 5\n";
if ($d->convLiteral('1011 binary', 'decimal') != 11) echo "FAIL 6\n";
if ($d->conv('auto', 'decimal', 'MCMLXXXVIII') != 1988)   echo "FAIL 7\n";

?>
