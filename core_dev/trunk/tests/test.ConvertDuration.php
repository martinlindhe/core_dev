<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertDuration.php');

$d = new ConvertDuration();

if ($d->conv('hour', 'second', 1) != 3600)       echo "FAIL 1\n";
if ($d->conv('day',  'minute', 4) != 5760)       echo "FAIL 2\n";
if ($d->convLiteral('1 hour', 'minutes') != 60)  echo "FAIL 3\n";
if ($d->convLiteral('4320 hour', 'days') != 180) echo "FAIL 4\n";

if ($d->convLiteral('1 gregorian year', 'second') != 31556952) echo "FAIL 5\n";
if ($d->convLiteral('1 julian year', 'second')    != 31557600) echo "FAIL 6\n";

?>
