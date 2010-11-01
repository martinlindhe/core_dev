<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('prop_Url.php');

// test url manipulation
$url = new Url();
$url->set('http://www.test.com/');
if ($url->getPath() != '/')                                 echo "FAIL 1\n";

$url->setParam('cat', 1);
if ($url->get() != 'http://www.test.com/?cat=1')            echo "FAIL 2 ".$url->get()."\n";
if ($url->getPath() != '/?cat=1')                           echo "FAIL 3\n";

$url->removeParam('cat');
$url->setParam('t', 'kalas');
$url->setParam('f', 'bas');
if ($url->get() != 'http://www.test.com/?t=kalas&f=bas')    echo "FAIL 4 ".$url->get()."\n";
if ($url->getPath() != '/?t=kalas&f=bas')                   echo "FAIL 5\n";

$url = new Url('http://test.com/?param');
if ($url->get() != 'http://test.com/?param')                echo "FAIL 6 ".$url->get()."\n";
if ($url->getPath() != '/?param')                           echo "FAIL 7\n";

$url = new Url('http://test.com/?p=n;hb=HEAD');
if ($url->get() != 'http://test.com/?p=n;hb=HEAD')          echo "FAIL 8 ".$url->get()."\n";
if ($url->getPath() != '/?p=n;hb=HEAD')                     echo "FAIL 9\n";

$url = new Url('http://test.com/?q=minuter+p%E5+skoj');
if ($url->get() != 'http://test.com/?q=minuter+p%E5+skoj')  echo "FAIL 10 ".$url->get()."\n";
if ($url->getPath() != '/?q=minuter+p%E5+skoj')             echo "FAIL 11\n";

?>
