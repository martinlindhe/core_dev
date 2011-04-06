<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('html.php');

if (htmlchars_decode('ja&nbsp;ha') != 'ja ha')  echo "FAIL 1\n"; // space char is a special NBSP character
if (htmlchars_decode('reg&reg;me') != 'reg®me') echo "FAIL 2\n";

?>
