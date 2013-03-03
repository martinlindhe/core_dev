<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertSpeed.php');

$arr = array(
array('mph',  'km/h', 18,  28.96816882546493962804),
array('km/h', 'm/s',  18,  5.000004),
array('ft/s', 'mph',  12,  8.18181818181818181818),
array('knot', 'm/s',  20,  10.28888),
);

foreach ($arr as $test)
{
    $res = ConvertSpeed::convert($test[0], $test[1], $test[2]);
    if ($res != $test[3])
        echo 'FAIL for '.$test[2].' '.$test[0].' => '.$test[1].', got '.$res.', expected '.$test[3]."\n";
}


