<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('sql_misc.php');

if (sql_date('') != '')     echo "FAIL 1\n";
if (sql_time('') != '')     echo "FAIL 2\n";
if (sql_datetime('') != '') echo "FAIL 3\n";

?>
