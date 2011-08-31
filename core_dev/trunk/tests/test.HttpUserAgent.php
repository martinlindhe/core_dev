<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('HttpUserAgent.php');


$s = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '2.0.0.11')
    echo 'FAIL 1: '.$s;

$s = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.8) Gecko/20100723 Ubuntu/10.04 (lucid) Firefox/3.6.8';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '3.6.8')
    echo 'FAIL 2: '.$s;

$s = 'Mozilla/5.0 (X11; Linux x86_64; rv:6.0) Gecko/20100101 Firefox/6.0';  // latest stable as of 2011-08-25
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '6.0')
    echo 'FAIL 3: '.$s;




$s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_7) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '11.0.696.68')
    echo 'FAIL 4: '.$s;

$s = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.113 Safari/534.30';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '12.0.742.113')
    echo 'FAIL 5: '.$s;

$s = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1'; // latest stable as of 2011-08-25
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '13.0.782.112')
    echo 'FAIL 6: '.$s;




$s = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '2.0.4')
    echo 'FAIL 7: '.$s;

$s = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/525.28 (KHTML, like Gecko) Version/3.2.2 Safari/525.28.1';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '3.2.2')
    echo 'FAIL 8: '.$s;

$s = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '4.0.5')
    echo 'FAIL 9: '.$s;

$s = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '5.0.4')
    echo 'FAIL 10: '.$s;




$s = 'Mozilla/4.0 (compatible; MSIE 4.01; Windows 98)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '4.01')
    echo 'FAIL 11: '.$s;

$s = 'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '5.23')
    echo 'FAIL 12: '.$s;

$s = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '6.0')
    echo 'FAIL 13: '.$s;

$s = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '7.0')
    echo 'FAIL 14: '.$s;

$s = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '8.0')
    echo 'FAIL 15: '.$s;

$s = 'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '9.0')
    echo 'FAIL 16: '.$s;



$s = 'Opera/9.00 (Windows NT 5.1; U; en)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '9.00')
    echo 'FAIL 17: '.$s;

$s = 'Opera/9.50 (Macintosh; Intel Mac OS X; U; en)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '9.50')
    echo 'FAIL 18: '.$s;

$s = 'Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.2.15 Version/10.00';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '10.00')
    echo 'FAIL 19: '.$s;

$s = 'Opera/9.80 (Windows NT 6.0; U; en) Presto/2.8.99 Version/11.10';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '11.10')
    echo 'FAIL 20: '.$s;

?>