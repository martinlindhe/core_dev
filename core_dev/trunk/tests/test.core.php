<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');

require_once('RegressionTest.php');


RegressionTest::check(array(
"strpre_exact('4523', 8, '0') != '00004523'",
"strpre_exact('4523', 3, '0') != '523'",
"strpad_exact('1234', 8, ' ') != '1234    '",
"strpad_exact('1234', 3, ' ') != '123'",
"is_alphanumeric('x\"x')",      //  " is NOT ok
"is_alphanumeric(\"x'x\")",     //  ' is NOT ok
"is_alphanumeric('abc 123')",   // space is NOT ok
"is_alphanumeric('abc/123')",   // slash is NOT ok
"!is_alphanumeric('abc123')",
"!is_alphanumeric('a-1')",
"!is_alphanumeric('a_2')",
"!is_alphanumeric('2öäåaÄÄÖÅ')",
"!is_alphanumeric('日本語')",
"!is_alphanumeric('한국어')",
"!is_alphanumeric('لقمة')",
"!is_alphanumeric('')",
"byte_count(1024 * 2) != '2 KiB'",
"byte_count(1024 * 1024 * 2) != '2 MiB'",
"byte_count(1024 * 1024 * 1024 * 2) != '2 GiB'",
"byte_count(1024 * 1024 * 1024 * 1024 * 2) != '2 TiB'",
"!instr('abc 123', 'bc')",
"instr('abc 123', 'cb')",
"instr('abc', 'aa')",
"instr('a', 'aa')",
"!instr('aa', 'a')",
"strip_spaces('  hello  ') != 'hello'",
)
);

?>
