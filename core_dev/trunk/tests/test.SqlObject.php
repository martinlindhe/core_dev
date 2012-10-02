<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SqlObject.php');

if (SqlObject::stringForm(123) != 'i')     echo "FAIL 1\n";
if (SqlObject::stringForm("abc") != 's')   echo "FAIL 2\n";
if (SqlObject::stringForm("0123") != 's')  echo "FAIL 3\n";

?>
