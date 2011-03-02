<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('JSON.php');
require_once('output_js.php');

if (JSON::encode(array(1,2,3)) != '[1,2,3]')   echo "FAIL 1\n";


$o = new stdClass();
$o->x = 1;
$o->y = 2;
$o->z = 3;
if (JSON::encode($o) != '{"x":1,"y":2,"z":3}') echo "FAIL 2\n";


if (jsArray1D(array('5-1' => 'a b c')) != '{"5-1":"a b c"}')   echo "FAIL 3\n";
if (jsArray1D(array('0123' => 'abc')) != '{"0123":"abc"}')     echo "FAIL 4\n";
if (jsArray1D(array('0123' => 0.5)) != '{"0123":0.5}')         echo "FAIL 5\n";
if (jsArray1D(array('0123' => 0)) != '{"0123":0}')             echo "FAIL 6\n";
if (jsArray1D(array('0123' => 1)) != '{"0123":1}')             echo "FAIL 7\n";
if (jsArray1D(array('123' => '0123')) != '{123:"0123"}')       echo "FAIL 8\n";
if (jsArray1D(array('123' => 'a.b')) != '{123:"a.b"}')         echo "FAIL 9\n";
if (jsArray1D(array('a.b' => '123')) != '{"a.b":123}')         echo "FAIL 10\n";
if (jsArray1D(array(0 => '0123')) != '{0:"0123"}')             echo "FAIL 11\n";
if (jsArray1D(array(1 => '0123')) != '{1:"0123"}')             echo "FAIL 12\n";
if (jsArray1D(array('a' => 1, 'b' => 2)) != '{"a":1,"b":2}')   echo "FAIL 13\n";

?>
