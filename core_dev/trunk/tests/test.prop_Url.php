<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('prop_Url.php');

// test url manipulation
$url = new Url();
$url->set('http://www.google.com/');
$url->setParam('category', 1);
if ($url->get() != 'http://www.google.com/?category=1')             echo "FAIL 1 ".$url->get()."\n";

$url->removeParam('category');
$url->setParam('test', 'kalas');
$url->setParam('fest', 'bas');
if ($url->get() != 'http://www.google.com/?test=kalas&fest=bas')    echo "FAIL 2 ".$url->get()."\n";

$url = new Url('http://www.google.com/?param');
if ($url->get() != 'http://www.google.com/?param')                  echo "FAIL 3 ".$url->get()."\n";

$url = new Url('http://git.ffmpeg.org/?p=ffmpeg;a=blob_plain;f=Changelog;hb=HEAD');
if ($url->get() != 'http://git.ffmpeg.org/?p=ffmpeg;a=blob_plain;f=Changelog;hb=HEAD') echo "FAIL 4 ".$url->get()."\n";

?>
