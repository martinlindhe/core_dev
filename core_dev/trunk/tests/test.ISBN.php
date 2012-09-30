<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ISBN.php');

//13-digit ISBN codes (EAN-13 compatible) has been in use since 2007
if (!ISBN::isValid('978-0-552-77429-1')) echo "FAIL 1\n";
if (!ISBN::isValid('978-91-7429-121-6')) echo "FAIL 2\n";
if (ISBN::isValid('978-91-7429-121-1'))  echo "FAIL 3\n"; // has bad checksum

?>
