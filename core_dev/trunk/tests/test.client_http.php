<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('client_http.php');

// test http client
$http = new HttpClient('http://www.google.com/');
$http->setCacheTime(0);
$body = $http->getBody();
if ($http->getHeader('content-type') != 'text/html; charset=UTF-8') echo "FAIL 1\n";
if ($http->getStatus() != 302)                                      echo "FAIL 2: ".$http->getStatus()."\n";

// test url validation
if (!is_url('http://server.com/file.php'))                          echo "FAIL 10\n";
if (!is_url('https://server.com/file.php'))                         echo "FAIL 11\n";
if (!is_url('http://server.com:1000/file.php'))                     echo "FAIL 12\n";
if (!is_url('http://server.com:80/file.php'))                       echo "FAIL 13\n";
if (!is_url('http://server.com/'))                                  echo "FAIL 14\n";

if (!is_url('http://server.com/path?arg=value'))                    echo "FAIL 15\n";
if (!is_url('http://server.com/path?arg=value#anchor'))             echo "FAIL 16\n";
if (!is_url('http://server.com/path?arg=value&arg2=4'))             echo "FAIL 17\n";
if (!is_url('http://server.com/path?arg=value&amp;arg2=4'))         echo "FAIL 18\n";

if (!is_url('http://username@server.com/path?arg=value'))           echo "FAIL 19\n";
if (!is_url('http://username:password@server.com/path?arg=value'))  echo "FAIL 20\n";

if (is_url('chaos'))                                                echo "FAIL 21\n";
if (is_url('chaos.com'))                                            echo "FAIL 22\n";
if (is_url('http://space in url.com/path.php'))                     echo "FAIL 23\n";

?>
