<?php

require('/var/www/core_dev/core/conv_datasize.php');

$d = new datasize();
if ($d->conv('megabyte', 'byte', 0.5) != 524288) echo "FAIL1\n";
if ($d->conv('megabit', 'megabyte', 100) != 12.5) echo "FAIL2\n";

?>
