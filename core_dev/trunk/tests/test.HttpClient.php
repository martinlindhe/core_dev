<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('HttpClient.php');


$http = new HttpClient('http://martin-rr.unicorn.se/');
$http->setDebug();
$http->addRequestHeader('Accept-Language: sv');
$body = $http->getBody();

d($http->getCookies() );
die;



$http = new HttpClient('http://www.if-not-true-then-false.com/');
$http->setDebug();
$body = $http->getBody();
d($http->getAllResponseHeaders() );
die;



$http = new HttpClient('http://www.google.com/');
$body = $http->getBody();
if ($http->getResponseHeader('content-type') != 'text/html; charset=UTF-8') echo "FAIL 1\n";
if ($http->getStatus() != 302)                                      echo "FAIL 2: ".$http->getStatus()."\n";

// see url validation tests in test.network.php

?>
