<?php

require_once('/var/www/core_dev/core/service_twitter.php');

$t = new twitter();

//if (!$t->test()) echo 'FAIL 1';

$x = $t->getTimeline('twitter'); print_r($x);

//need auth:
//$x = $t->getFriendsTimeline(); print_r($x);
//$t->post('testar lite');

?>
