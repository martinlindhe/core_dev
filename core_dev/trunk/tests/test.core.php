<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');

require_once('RegressionTest.php');


RegressionTest::check(__FILE__, array(
array("strpre_exact('4523', 8, '0')",     '00004523'),
array("strpre_exact('4523', 3, '0')",     '523'),
array("strpad_exact('1234', 8, ' ')",     '1234    '),
array("strpad_exact('1234', 3, ' ')",     '123'),
array("is_alphanumeric('x\"x')",           false),  //  " is NOT ok
array("is_alphanumeric(\"x'x\")",          false),  //  ' is NOT ok
array("is_alphanumeric('abc 123')",        false),  // space is NOT ok
array("is_alphanumeric('abc/123')",        false),  // slash is NOT ok
array("is_alphanumeric('abc123')",         true),
array("is_alphanumeric('a-1')",            true),
array("is_alphanumeric('a_2')",            true),
array("is_alphanumeric('2öäåaÄÄÖÅ')",      true),
array("is_alphanumeric('日本語')",          true),   // utf8 is ok
array("is_alphanumeric('한국어')",           true),
array("is_alphanumeric('لقمة')",           true),
array("is_alphanumeric('')",               true),
array("byte_count(1024*2)",                '2 KiB'),
array("byte_count(1024*1024*2)",           '2 MiB'),
array("byte_count(1024*1024*1024*2)",      '2 GiB'),
array("byte_count(1024*1024*1024*1024*2)", '2 TiB'),
array("instr('abc 123', 'bc')",            true),
array("instr('abc 123', 'cb')",            false),
array("instr('abc', 'aa')",                false),
array("instr('a', 'aa')",                  false),
array("instr('aa', 'a')",                  true),
array("strip_spaces(' h  ell o  ')",       'hello'),
));

?>
