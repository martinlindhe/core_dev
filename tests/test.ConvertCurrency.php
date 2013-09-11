<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('ConvertCurrency.php');

$val = 100;
echo $val." USD is currently worth ".ConvertCurrency::convert('USD', 'SEK', $val)." SEK\n";

?>
