<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');

if (strpre_exact('4523', 8, '0') != '00004523') die('FAIL 1');
if (strpre_exact('4523', 3, '0') != '523') die('FAIL 2');
if (strpad_exact('1234', 8, ' ') != '1234    ') die('FAIL 3');
if (strpad_exact('1234', 3, ' ') != '123') die('FAIL 4');

?>
