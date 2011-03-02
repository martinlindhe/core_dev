<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('bits.php');

if (byte_split(0xfa) != array(15, 10))   echo "FAIL 1\n";
if (byte_split(0xff) != array(15, 15))   echo "FAIL 2\n";
if (byte_split('D') != array(4, 4))      echo "FAIL 3\n";  // "D" = 0x44
if (byte_split("\xca") != array(12, 10)) echo "FAIL 4\n";

if (byte_to_bits(0x01) != array(7=>0,6=>0,5=>0,4=>0,3=>0,2=>0,1=>0,0=>1)) echo "FAIL 5\n";
if (byte_to_bits(0xff) != array(7=>1,6=>1,5=>1,4=>1,3=>1,2=>1,1=>1,0=>1)) echo "FAIL 6\n";
if (byte_to_bits(0x80) != array(7=>1,6=>0,5=>0,4=>0,3=>0,2=>0,1=>0,0=>0)) echo "FAIL 7\n";

?>
