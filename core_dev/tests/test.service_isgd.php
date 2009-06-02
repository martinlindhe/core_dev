<?php
require_once('/var/www/core_dev/core/service_isgd.php');

$x = 'http://www.dn.se/kultur-noje/musik/musikbranschen-varnas-for-varningsbrev-1.881738';
if (isgdShortURL($x) != 'http://is.gd/Mj3V') echo 'FAIL 1';

?>
