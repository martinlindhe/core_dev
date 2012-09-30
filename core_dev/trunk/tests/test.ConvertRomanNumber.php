<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertRomanNumber.php');

if (!ConvertRomanNumber::isValid('XII')) echo "FAIL 1\n";
if (ConvertRomanNumber::isValid('MMC1')) echo "FAIL 2\n";

$x = new ConvertRomanNumber('XIV');
if ($x->getAsRoman() != 'XIV') echo "FAIL 3\n";

$x = new ConvertRomanNumber('MMX');
if ($x->getAsInteger() != 2010) echo "FAIL 4\n";

$x = new ConvertRomanNumber('MCMXCIX');
if ($x->getAsInteger() != 1999) echo "FAIL 5\n";

$x = new ConvertRomanNumber(1988);
if ($x->getAsRoman() != 'MCMLXXXVIII') echo "FAIL 6\n";

$x = new ConvertRomanNumber('MMMMCMXCIX');
if ($x->getAsInteger() != 4999) echo "FAIL 7\n";

?>
