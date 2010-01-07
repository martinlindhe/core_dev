<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('client_http.php');

// test http client
$http = new HttpClient('http://www.google.com/');
$http->setCacheTime(0);
$body = $http->getBody();
if ($http->getHeader('content-type') != 'text/html; charset=UTF-8') echo "FAIL 1\n";
if ($http->getStatus() != 302) echo "FAIL 2: ".$http->getStatus()."\n";


// test url manipulation
$url = new Url();
$url->set('http://www.google.com/');
$url->setParam('category', 1);
if ($url->get() != 'http://www.google.com/?category=1') echo "FAIL 10\n";
$url->removeParam('category');
$url->setParam('test', 'kalas');
$url->setParam('fest', 'bas');
if ($url->get() != 'http://www.google.com/?test=kalas&fest=bas') echo "FAIL 11\n";


// test url validation
if (!is_url('http://server.com/file.php')) echo "FAIL 20\n";
if (!is_url('https://server.com/file.php')) echo "FAIL 21\n";
if (!is_url('http://server.com:1000/file.php')) echo "FAIL 22\n";
if (!is_url('http://server.com:80/file.php')) echo "FAIL 23\n";
if (!is_url('http://server.com/')) echo "FAIL 24\n";

if (!is_url('http://server.com/path?arg=value')) echo "FAIL 25\n";
if (!is_url('http://server.com/path?arg=value#anchor')) echo "FAIL 26\n";
if (!is_url('http://server.com/path?arg=value&arg2=4')) echo "FAIL 27\n";
if (!is_url('http://server.com/path?arg=value&amp;arg2=4')) echo "FAIL 28\n";

if (!is_url('http://username@server.com/path?arg=value')) echo "FAIL 29\n";
if (!is_url('http://username:password@server.com/path?arg=value')) echo "FAIL 30\n";

if (is_url('chaos')) echo "FAIL 31\n";
if (is_url('chaos.com')) echo "FAIL 32\n";
if (is_url('http://space in url.com/path.php')) echo "FAIL 33\n";

?>
