<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('output_js.php');

// test escaping of non-numeric keys
if (jsArray1D(array('5-1' => 'a b c')) != '{"5-1":"a b c"}')   echo "FAIL 1\n";
if (jsArray1D(array('0123' => 'abc')) != '{"0123":"abc"}')     echo "FAIL 2\n";
if (jsArray1D(array('0123' => 0.5)) != '{"0123":0.5}')         echo "FAIL 3\n";
if (jsArray1D(array('0123' => 0)) != '{"0123":0}')             echo "FAIL 4\n";
if (jsArray1D(array('0123' => 1)) != '{"0123":1}')             echo "FAIL 5\n";
if (jsArray1D(array('123' => '0123')) != '{123:"0123"}')       echo "FAIL 6\n";
if (jsArray1D(array('123' => 'a.b')) != '{123:"a.b"}')         echo "FAIL 7\n";
if (jsArray1D(array('a.b' => '123')) != '{"a.b":123}')         echo "FAIL 8\n";

?>
