<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('sql_misc.php');

if (sql_date('') != '')     echo "FAIL 1\n";
if (sql_time('') != '')     echo "FAIL 2\n";
if (sql_datetime('') != '') echo "FAIL 3\n";

if (sql_date('0000-00-00') != '0000-00-00') echo "FAIL 4\n"; // date got converted to "-0001-11-30" without override

?>
