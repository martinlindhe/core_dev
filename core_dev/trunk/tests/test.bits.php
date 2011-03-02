<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('bits.php');

if (byte_split(0xfa) != array(15, 10)) echo "FAIL 1\n";

?>
