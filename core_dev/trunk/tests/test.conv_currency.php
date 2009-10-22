<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('conv_currency.php');

$currency = new currency();
$val = 100;
echo $val." USD is currently worth ".$currency->conv('USD', 'SEK', $val)." SEK\n";

?>
