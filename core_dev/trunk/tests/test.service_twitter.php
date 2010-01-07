<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_twitter.php');

$t = new Twitter();
if (!$t->test()) echo 'FAIL 1';

//print_r( $t->getTimeline('twitter') );
//print_r( $t->getSearchResult('#crap') );



//needs auth:
/*
$t->setUsername('xxx');
$t->setPassword('xxx');
$x = $t->getFriendsTimeline(); print_r($x);
$t->post('testar lite');
*/

?>
