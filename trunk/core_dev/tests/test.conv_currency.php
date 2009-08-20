<?php
require('/var/www/core_dev/core/conv_currency.php');

$currency = new currency();
$val = 100;
echo $val." USD is currently worth ".$currency->conv('USD', 'SEK', $val)." SEK\n";

?>
