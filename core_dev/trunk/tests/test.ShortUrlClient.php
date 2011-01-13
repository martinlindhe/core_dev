<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IShortUrlClient.php');

$url = 'http://developer.yahoo.com/yui/editor/';
echo 'is.gd:   '.ShortUrlClientIsGd::shorten($url)."\n";
echo 'tinyurl: '.ShortUrlClientTinyUrl::shorten($url)."\n";
echo 'bit.ly: '.ShortUrlClientBitLy::shorten($url)."\n";
echo 'goo.gl: '.ShortUrlClientGooGl::shorten($url)."\n";

?>
