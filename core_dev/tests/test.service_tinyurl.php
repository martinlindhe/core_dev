<?php

require_once('/var/www/core_dev/core/service_tinyurl.php');

$x = 'http://www.dn.se/kultur-noje/musik/musikbranschen-varnas-for-varningsbrev-1.881738';
if (tinyurlShortURL($x) != 'http://tinyurl.com/klobp7') echo 'FAIL 1';

?>
