<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');

require_once('RegressionTest.php');

RegressionTest::check(__FILE__, array(
array("strpre_exact('4523', 8, '0')",       '00004523'),
array("strpre_exact('4523', 3, '0')",       '523'),
array("strpad_exact('1234', 8, ' ')",       '1234    '),
array("strpad_exact('1234', 3, ' ')",       '123'),
array("is_alphanumeric('2')",               true),
array("is_alphanumeric('2.0')",             true),
array("is_alphanumeric('%')",               false),  // important not to allow control characters
array("is_alphanumeric('/')",               false),  // important not to allow control characters
array("is_alphanumeric('x\"x')",            false),  //  " is NOT ok
array("is_alphanumeric(\"x'x\")",           false),  //  ' is NOT ok
array("is_alphanumeric('abc 123')",         false),  // space is NOT ok
array("is_alphanumeric('abc/123')",         false),  // slash is NOT ok
array("is_alphanumeric('abc123')",          true),
array("is_alphanumeric('a-1')",             true),
array("is_alphanumeric('a_2')",             true),
array("is_alphanumeric('2öäåaÄÄÖÅ')",       true),
array("is_alphanumeric('日本語')",          true),   // utf8 is ok
array("is_alphanumeric('한국어')",           true),
array("is_alphanumeric('لقمة')",            true),
array("is_alphanumeric('')",                true),
array("byte_count(1024*2)",                 '2 KiB'),
array("byte_count(1024*1024*2)",            '2 MiB'),
array("byte_count(1024*1024*1024*2)",       '2 GiB'),
array("byte_count(1024*1024*1024*1024*2)",  '2 TiB'),
array("instr('abc 123', 'bc')",             true),
array("instr('abc 123', 'cb')",             false),
array("instr('abc', 'aa')",                 false),
array("instr('a', 'aa')",                   false),
array("instr('aa', 'a')",                   true),
array("strip_spaces(' h  ell o  ')",        'hello'),
array("sbool(true)",                        'true'),
array("sbool(false)",                       'false'),
array("string_to_bool('true')",             true),
array("string_to_bool('false')",            false),
array("bool_to_int(true)",                  1),
array("bool_to_int(false)",                 0),
array("numbers_only('123')",                true),
array("numbers_only('0')",                  true),
array("numbers_only('12a')",                false),
array("numbers_only('12.0')",               false),
array("numbers_only('')",                   false),
array("is_number_range('2-0')",             true),
array("is_number_range('2000-3000')",       true),
array("str_between('--abcxx', '--', 'xx')", 'abc'),
array("str_between('-- abc xx', '--', 'xx')", ' abc '),
array("str_between('-1-', '-', '-')",       '1'),
array("str_between('a1aa', 'a', 'a')",      '1'),
array("str_remaining('one two three', ' two ')",    'three'),
array("str_remaining('one TWO three', ' two ')",    false),
array("formatMSID('0707123456', '46')",     '46707123456'),
array("formatMSID('0707-123 456', '46')",   '46707123456'),
array("formatMSID('46707123456', '46')",    '46707123456'),
array("formatMSID('0046707123456', '46')",  '46707123456'), // 46 is country code for Sweden
array("formatMSID('04612345', '46')",       '464612345'),   // 046 is area code for Lund, Sweden
array("formatMSID('0044', '46')",           '0044'),        // dont touch short special codes
array("is_upper_char('A')",                 true),
array("is_upper_char('Å')",                 true),
array("is_upper_char('a')",                 false),
array("is_upper_char('å')",                 false),
array("is_upper_char('Z')",                 true),
array("is_upper_char('z')",                 false),
array("is_lower_char('A')",                 false),
array("is_lower_char('a')",                 true),
array("is_lower_char('Z')",                 false),
array("is_lower_char('z')",                 true),
array("is_upper_str(\"AAA\")",              true),
array("is_upper_str(\"AaA\")",              false),
array("is_upper_str(\"AAa\")",              false),
array("is_upper_str(\"ÅAA\")",              true),
array("is_upper_str(\"Åaa\")",              false),
array("is_upper_str(\"AAå\")",              false),
array("is_upper_str(\"PÅ\")",               true),
array("is_lower_str(\"på\")",               true),
array("is_lower_str(\"pÅ\")",               false),
array("is_ucfirst_str(\"Hallå\")",          true),
array("is_ucfirst_str(\"HallÅ\")",          false),
array("is_ucfirst_str(\"HALLÅ\")",          false),
));

