<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ISBN.php');

if (!ISBN::isValid('978-0-552-77429-1')) echo "FAIL 1\n"; //13-digit ISBN (EAN-13 compatible) has been in use since 2007

?>
