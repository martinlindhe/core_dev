<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('conv_duration.php');

$d = new ConvertDuration();

if ($d->conv('hour', 'second', 1) != 3600) echo "FAIL1\n";
if ($d->conv('day',  'minute', 4) != 5760) echo "FAIL2\n";

?>
