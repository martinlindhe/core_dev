<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('HttpUserAgent.php');


// FIREFOX:
$s = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '2.0.0.11')
    echo 'FAIL 1: '.$s."\n";

$s = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.8) Gecko/20100723 Ubuntu/10.04 (lucid) Firefox/3.6.8';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '3.6.8')
    echo 'FAIL 2: '.$s."\n";

$s = 'Mozilla/5.0 (X11; Linux x86_64; rv:6.0) Gecko/20100101 Firefox/6.0';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '6.0' || $b->os != 'X11' || $b->arch != 'Linux x86_64')
    echo 'FAIL 3: '.$s."\n";

$s = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '9.0.1' || $b->os != 'Windows NT 6.1' || $b->arch != 'WOW64')
    echo 'FAIL 4: '.$s."\n";

$s = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '10.0' || $b->os != 'X11' || $b->arch != 'Linux x86_64')
    echo 'FAIL 5: '.$s."\n";

$s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:10.0) Gecko/20100101 Firefox/10.0';  // latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '10.0' || $b->os != 'Macintosh' || $b->arch != 'Intel Mac OS X 10.7')
    echo 'FAIL 6: '.$s."\n";



// CHROME:
$s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_7) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '11.0.696.68')
    echo 'FAIL 10: '.$s."\n";

$s = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.113 Safari/534.30';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '12.0.742.113')
    echo 'FAIL 11: '.$s."\n";

$s = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '16.0.912.75' || $b->os != 'Windows NT 6.1' || $b->arch != 'WOW64')
    echo 'FAIL 12: '.$s."\n";

$s = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '16.0.912.77' || $b->os != 'X11' || $b->arch != 'Linux x86_64')
    echo 'FAIL 13: '.$s."\n";

$s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7'; // latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '16.0.912.77' || $b->os != 'Macintosh' || $b->arch != 'Intel Mac OS X 10_7_3')
    echo 'FAIL 14: '.$s."\n";



// SAFARI:
$s = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '2.0.4' || $b->os != 'Macintosh' || $b->arch != 'Intel Mac OS X')
    echo 'FAIL 20: '.$s."\n";

$s = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/525.28 (KHTML, like Gecko) Version/3.2.2 Safari/525.28.1';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '3.2.2' || $b->os != 'Windows' || $b->arch != 'Windows NT 5.2')
    echo 'FAIL 21: '.$s."\n";

$s = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '4.0.5' || $b->os != 'Windows' || $b->arch != 'Windows NT 6.0')
    echo 'FAIL 22: '.$s."\n";

$s = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '5.0.4' || $b->os != 'Windows' || $b->arch != 'Windows NT 6.1')
    echo 'FAIL 23: '.$s."\n";

$s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.53.11 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10'; // latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '5.1.3' || $b->os != 'Macintosh' || $b->arch != 'Intel Mac OS X 10_7_3')
    echo 'FAIL 24: '.$s."\n";



// SAFARI iOS:
$s = 'Mozilla/5.0 (iPhone; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10';
$b = HttpUserAgent::getBrowser($s);
if (!HttpUserAgent::isIOS($s) || $b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '4.0.4' || $b->os != 'iPhone' || $b->arch != 'CPU OS 3_2 like Mac OS X')
    echo 'FAIL 30: '.$s."\n";

$s = 'Mozilla/5.0 (iPad;U;CPU OS 3_2_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B500 Safari/531.21.10';
$b = HttpUserAgent::getBrowser($s);
if (!HttpUserAgent::isIOS($s) || $b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '4.0.4' || $b->os != 'iPad' || $b->arch != 'CPU OS 3_2_2 like Mac OS X')
    echo 'FAIL 31: '.$s."\n";

$s = 'Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; ja-jp) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5';
$b = HttpUserAgent::getBrowser($s);
if (!HttpUserAgent::isIOS($s) || $b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '5.0.2' || $b->os != 'iPod' || $b->arch != 'CPU iPhone OS 4_3_3 like Mac OS X')
    echo 'FAIL 32: '.$s."\n";

$s = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A405 Safari/7534.48.3'; // latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if (!HttpUserAgent::isIOS($s) || $b->vendor != 'Apple' || $b->name != 'Safari' || $b->version != '5.1' || $b->os != 'iPhone' || $b->arch != 'CPU iPhone OS 5_0_1 like Mac OS X')
    echo 'FAIL 33: '.$s."\n";



// INTERNET EXPLORER:
$s = 'Mozilla/4.0 (compatible; MSIE 4.01; Windows 98)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '4.01')
    echo 'FAIL 40: '.$s."\n";

$s = 'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '5.23')
    echo 'FAIL 41: '.$s."\n";

$s = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '6.0')
    echo 'FAIL 42: '.$s."\n";

$s = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '7.0')
    echo 'FAIL 43: '.$s."\n";

$s = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '8.0')
    echo 'FAIL 44: '.$s."\n";

$s = '"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)';  //latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Microsoft' || $b->name != 'Internet Explorer' || $b->version != '9.0')
    echo 'FAIL 45: '.$s."\n";



// OPERA:
$s = 'Opera/9.00 (Windows NT 5.1; U; en)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '9.00' || $b->os != 'Windows NT 5.1')
    echo 'FAIL 50: '.$s."\n";

$s = 'Opera/9.50 (Macintosh; Intel Mac OS X; U; en)';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '9.50' || $b->os != 'Macintosh')
    echo 'FAIL 51: '.$s."\n";

$s = 'Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.2.15 Version/10.00';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '10.00' || $b->os != 'X11')
    echo 'FAIL 52: '.$s."\n";

$s = 'Opera/9.80 (Windows NT 6.0; U; en) Presto/2.8.99 Version/11.10';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '11.10' || $b->os != 'Windows NT 6.0')
    echo 'FAIL 53: '.$s."\n";

$s = 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61';  // latest stable as of 2012-02-08
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Opera Software' || $b->name != 'Opera' || $b->version != '11.61' || $b->os != 'Windows NT 6.1')
    echo 'FAIL 54: '.$s."\n";

?>
