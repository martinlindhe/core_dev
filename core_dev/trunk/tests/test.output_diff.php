<?
require_once('/var/www/core_dev/trunk/core/output_diff.php');

$x1 = "din mamma heter kallops\nDin med!\n";
$x2 = "min mamma heter kallops\nDin med!\n";

$diff = new diff();

echo $diff->strings($x1, $x2);
?>
