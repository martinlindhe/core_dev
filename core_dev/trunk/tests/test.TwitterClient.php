<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('TwitterClient.php');

$t = new TwitterClient();
//if (!$t->test()) echo 'FAIL 1';


$x = $t->getTimeline('phonecafedrift');

print_r($x);


//XXX FUNKAR EJ NU PGA SAKNAD OAUTH SUPPORT:
//print_r( $t->getSearchResult('kex') );
//$x = $t->getFriendsTimeline();
//print_r($x);

?>
