<?php

require_once('/var/www/core_dev/trunk/core/service_twitter.php');

$t = new Twitter();

//if (!$t->test()) echo 'FAIL 1';

//$x = $t->getTimeline('twitter'); print_r($x);


$x = $t->getSearchResult('#crap'); print_r($x);


//needs auth:
/*
$t->setUsername('xxx');
$t->setPassword('xxx');
$x = $t->getFriendsTimeline(); print_r($x);
$t->post('testar lite');
*/

?>
