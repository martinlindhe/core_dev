<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertMass.php');

$arr = array(
array('kg', 'lb',   500,  1102.31131092438790361486),
array('lb', 'kg',   500,  226.796185),
array('t',  'kg',   1,    1000),
array('kg', 't',    2000, 2),
array('oz', 'g',    1,    28.349523125),
array('kg', 'gram', 1,    1000),
);

foreach ($arr as $test)
{
    $res = ConvertMass::convert($test[0], $test[1], $test[2]);
    if ($res != $test[3])
        echo 'FAIL for '.$test[2].' '.$test[0].' => '.$test[1].', got '.$res.', expected '.$test[3]."\n";
}


