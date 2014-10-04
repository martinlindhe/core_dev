<?php

namespace cd;

echo '<h1>Currencies</h1>';

echo countryFlag('EUR').' 1 EUR = '.round(ConvertCurrency::convert('EUR', 'SEK', 1), 2)." SEK<br/>";
echo countryFlag('USA').' 1 USD = '.round(ConvertCurrency::convert('USD', 'SEK', 1), 2)." SEK<br/>";
echo countryFlag('JPN').' 1 JPY = '.round(ConvertCurrency::convert('JPY', 'SEK', 1), 2)." SEK<br/>";
echo '<br/>';

$val = 100;
echo countryFlag('SWE').' '.$val." SEK is currently worth:<br/>";
echo countryFlag('EUR').' '.round(ConvertCurrency::convert('SEK', 'EUR', $val), 2)." EUR<br/>";
echo countryFlag('USA').' '.round(ConvertCurrency::convert('SEK', 'USD', $val), 2)." USD<br/>";
echo countryFlag('JPN').' '.round(ConvertCurrency::convert('SEK', 'JPY', $val), 2)." JPY<br/>";
echo countryFlag('NOR').' '.round(ConvertCurrency::convert('SEK', 'NOK', $val), 2)." NOK<br/>";
echo countryFlag('DNK').' '.round(ConvertCurrency::convert('SEK', 'DKK', $val), 2)." DKK<br/>";

echo '<br/>';
