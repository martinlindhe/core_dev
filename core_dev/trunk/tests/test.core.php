<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');

if (strpre_exact('4523', 8, '0') != '00004523') echo "FAIL 1\n";
if (strpre_exact('4523', 3, '0') != '523')      echo "FAIL 2\n";
if (strpad_exact('1234', 8, ' ') != '1234    ') echo "FAIL 3\n";
if (strpad_exact('1234', 3, ' ') != '123')      echo "FAIL 4\n";


if (is_alphanumeric('x"x'))        echo "FAIL 5\n";  //  " is NOT ok
if (is_alphanumeric("x'x"))        echo "FAIL 6\n";  //  ' is NOT ok
if (is_alphanumeric('abc 123'))    echo "FAIL 7\n";  // space is NOT ok
if (is_alphanumeric('abc/123'))    echo "FAIL 8\n";  // / is NOT ok

if (!is_alphanumeric('abc123'))    echo "FAIL 9\n";
if (!is_alphanumeric('a-1'))       echo "FAIL 10\n"; // - is ok
if (!is_alphanumeric('a_2'))       echo "FAIL 11\n"; // _ is ok

if (!is_alphanumeric('2öäåaÄÄÖÅ')) echo "FAIL 12\n"; // utf8 is ok
if (!is_alphanumeric('日本語'))     echo "FAIL 13\n"; // utf8 is ok
if (!is_alphanumeric('한국어'))      echo "FAIL 14\n"; // utf8 is ok
if (!is_alphanumeric('لقمة'))      echo "FAIL 15\n"; // utf8 is ok

if (!is_alphanumeric(''))          echo "FAIL 16\n";

?>
