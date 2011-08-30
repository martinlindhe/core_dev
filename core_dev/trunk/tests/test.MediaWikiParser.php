<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('MediaWikiClient.php');

$s = 'http://en.wikipedia.org/wiki/C%2B%2B';  // C++

$x = MediaWikiClient::getArticleTitle($s);

echo $x."\n";

?>
