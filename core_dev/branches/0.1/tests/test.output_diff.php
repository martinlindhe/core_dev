<?php
require_once('/var/www/core_dev/core/output_diff.php');

$x1 = "din mamma heter kallops\nDin med!\n";
$x2 = "min mamma heter kallops\nDin med!\n";

$d = new diff();
$d->strings($x1, $x2);
$d->output();
?>
