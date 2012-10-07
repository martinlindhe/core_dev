<?php

namespace cd;

require_once('ConvertCurrency.php');

echo '<h1>Currencies</h1>';

$currency = new ConvertCurrency();

echo countryFlag('EUR').' 1 EUR = '.$currency->conv('EUR', 'SEK', 1, 2)." SEK<br/>";
echo countryFlag('USA').' 1 USD = '.$currency->conv('USD', 'SEK', 1, 2)." SEK<br/>";
echo countryFlag('JPN').' 1 JPY = '.$currency->conv('JPY', 'SEK', 1, 2)." SEK<br/>";
echo '<br/>';

$val = 100;
echo countryFlag('SWE').' '.$val." SEK is currently worth:<br/>";
echo countryFlag('EUR').' '.$currency->conv('SEK', 'EUR', $val, 2)." EUR<br/>";
echo countryFlag('USA').' '.$currency->conv('SEK', 'USD', $val, 2)." USD<br/>";
echo countryFlag('JPN').' '.$currency->conv('SEK', 'JPY', $val, 2)." JPY<br/>";

echo countryFlag('NOR').' '.$currency->conv('SEK', 'NOK', $val, 2)." NOK<br/>";
echo countryFlag('DNK').' '.$currency->conv('SEK', 'DKK', $val, 2)." DKK<br/>";

echo '<br/>';


?>
