<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('client_http.php');

$u = new HttpClient('http://www.google.cn/');
//$u->setDebug();
$u->setCacheTime(0);
$body = $u->getBody();
d($body);
die;

echo 'mime type: '.http_content_type( $u->head() ). "\n";
die;





echo 'in : '.$url."\n";
$u->add('category', 0);
$u->remove('type');
echo 'out: '.$u->render()."\n";


if (!is_url('http://server.com/file.php')) echo "FAIL 1\n";
if (!is_url('https://server.com/file.php')) echo "FAIL 2\n";
if (!is_url('http://server.com:1000/file.php')) echo "FAIL 3\n";
if (!is_url('http://server.com:80/file.php')) echo "FAIL 4\n";
if (!is_url('http://server.com/')) echo "FAIL 5\n";

if (!is_url('http://server.com/path?arg=value')) echo "FAIL 6\n";
if (!is_url('http://server.com/path?arg=value#anchor')) echo "FAIL 7\n";
if (!is_url('http://server.com/path?arg=value&arg2=4')) echo "FAIL 8\n";
if (!is_url('http://server.com/path?arg=value&amp;arg2=4')) echo "FAIL 9\n";

if (!is_url('http://username@server.com/path?arg=value')) echo "FAIL 10\n";
if (!is_url('http://username:password@server.com/path?arg=value')) echo "FAIL 11\n";

if (is_url('chaos')) echo "FAIL 12\n";
if (is_url('chaos.com')) echo "FAIL 13\n";
if (is_url('http://space in url.com/path.php')) echo "FAIL 14\n";



$url = 'http://www.google.com/';
if (http_status($url) != 302) echo "FAIL 15: ".http_status($url)."\n";

$headers = http_head($url);
echo "\nheaders:\n";
print_r($headers)."\n";

echo "last modified: ".formatTime(http_last_modified($headers))."\n";
echo "content-length: ".http_content_length($headers)."\n";

?>
