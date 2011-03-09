<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('HttpClient.php');

$http = new HttpClient('http://www.if-not-true-then-false.com/'); // this domain returns "gzip" compressed documents
$body = $http->getBody();
d($body);



$http = new HttpClient('http://www.google.com/');
$body = $http->getBody();
if ($http->getResponseHeader('content-type') != 'text/html; charset=UTF-8') echo "FAIL 1\n";
if ($http->getStatus() != 302)                                      echo "FAIL 2: ".$http->getStatus()."\n";

// see url validation tests in test.network.php

?>
