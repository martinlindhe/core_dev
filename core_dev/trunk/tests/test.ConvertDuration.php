<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertDuration.php');

$arr = array(
array('hour', 'second', 1, 3600),
array('day',  'minute', 4, 5760),
array('hour', 'minutes', 1, 60),
array('hour', 'days', 4320, 180),
array('week', 'days', 2, 14),
array('gregorian year', 'second', 1, 31556952),
array('julian year', 'second', 1, 31557600),
array('millisecond', 'second', 1, 0.001),
array('centisecond', 'second', 2, 0.02),
array('decisecond',  'second', 4, 0.4),
array('microsecond',  'millisecond', 2, 0.002),
array('nanosecond',   'microsecond', 2, 0.002),
array('picosecond',   'nanosecond', 2, 0.002),
array('ky', 'year', 2, 2000),
array('femtosecond', 'attosecond', 1, 1000),
array('attosecond', 'second', 100000000000000000, 0.1),
array('zeptosecond', 'attosecond', 100, 0.1),
);

foreach ($arr as $test)
{
    $res = ConvertDuration::convert($test[0], $test[1], $test[2]);
    if ($res != $test[3])
        echo 'FAIL for '.$test[2].' '.$test[0].' => '.$test[1].', got '.$res.', expected '.$test[3]."\n";
}

