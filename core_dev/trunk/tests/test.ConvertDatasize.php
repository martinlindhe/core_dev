<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertDatasize.php');

$d = new ConvertDatasize();
if ($d->conv('megabyte', 'byte', 0.5) != 524288)  echo "FAIL 1\n";
if ($d->conv('megabit', 'megabyte', 100) != 12.5) echo "FAIL 2\n";
if ($d->convLiteral('1GB', 'MiB') != 1024)        echo "FAIL 3\n";

?>
