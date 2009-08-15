<?php

require_once('/var/www/core_dev/core/service_twitter.php');

$t = new twitter('xxx','xxx');

//if (!$t->test()) echo 'FAIL 1';

$x = $t->getTimeline(); print_r($x);

//$t->post('testar lite');


//$t->update('helloouu');


?>
