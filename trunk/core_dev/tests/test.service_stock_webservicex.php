<?php

require_once('/var/www/core_dev/core/service_stock_webservicex.php');

$x = webservicex_stock_quote('AAPL');
print_r($x);

?>
