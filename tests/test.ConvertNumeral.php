<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertNumeral.php');

$arr = array(
array('decimal', 'binary',  '11',   1011),
array('decimal', 'octal',   '44',   54),
array('octal',   'decimal', '33',   27),
array('octal',   'decimal', '1234', 668),
array('binary',  'decimal', '1110', 14),
array('binary',  'decimal', '1011', 11),
);

foreach ($arr as $test)
{
    $res = ConvertNumeral::convert($test[0], $test[1], $test[2]);
    if ($res != $test[3])
        echo 'FAIL for '.$test[2].' '.$test[0].' => '.$test[1].', got '.$res.', expected '.$test[3]."\n";
}


