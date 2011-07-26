<?php

require_once('ConvertCurrency.php');

echo '<h1>Currencies</h1>';

$currency = new ConvertCurrency();
$val = 100;

echo countryFlag('SWE').' '.$val." SEK is currently worth:<br/><br/>";
echo countryFlag('NOR').' '.$currency->conv('SEK', 'NOK', $val)." NOK<br/>";
echo countryFlag('DNK').' '.$currency->conv('SEK', 'DKK', $val)." DKK<br/>";
echo countryFlag('EU').' '.$currency->conv('SEK', 'EUR', $val)." EUR<br/>";
echo countryFlag('USA').' '.$currency->conv('SEK', 'USD', $val)." USD<br/>";

?>
