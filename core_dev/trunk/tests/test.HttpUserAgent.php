<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('HttpUserAgent.php');


// Latest stable Firefox, 2011-08-25:
$s = 'Mozilla/5.0 (X11; Linux x86_64; rv:6.0) Gecko/20100101 Firefox/6.0';
$b = HttpUserAgent::getBrowser($s);
if ($b->vendor != 'Mozilla' || $b->name != 'Firefox' || $b->version != '6.0')
    echo 'FAIL 1: '.$s;


// Latest stable Chrome, 2011-08-25:
$s = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1';
$b = HttpUserAgent::getBrowser($s);

if ($b->vendor != 'Google' || $b->name != 'Chrome' || $b->version != '13.0.782.112')
    echo 'FAIL 2: '.$s;

?>
