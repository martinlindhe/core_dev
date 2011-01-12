<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IShortUrlClient.php');

$url = 'http://developer.yahoo.com/yui/editor/';
echo 'is.gd:   '.ShortUrlClientIsGd::getShortUrl($url)."\n";
echo 'tinyurl: '.ShortUrlClientTinyUrl::getShortUrl($url)."\n";

?>
