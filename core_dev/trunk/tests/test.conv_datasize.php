<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('conv_datasize.php');

$d = new datasize();
if ($d->conv('megabyte', 'byte', 0.5) != 524288) echo "FAIL1\n";
if ($d->conv('megabit', 'megabyte', 100) != 12.5) echo "FAIL2\n";

?>
