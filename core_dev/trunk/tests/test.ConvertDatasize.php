<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertDatasize.php');

$arr = array(
array('megabyte',  'byte',     0.5,  524288),
array('megabit',   'megabyte', 100,  12.5),
array('GB',        'MiB',      1,    1024),
array('zb',        'tb',       1,    1073741824),
array('zettabyte', 'exabyte',  1,    1024),
array('zettabit',  'exabit',   1,    1024),
);

foreach ($arr as $test)
{
    $res = ConvertDatasize::convert($test[0], $test[1], $test[2]);
    if ($res != $test[3])
        echo 'FAIL for '.$test[2].' '.$test[0].' => '.$test[1].', got '.$res.', expected '.$test[3]."\n";
}

