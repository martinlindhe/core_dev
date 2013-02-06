<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('MediaWikiClient.php');

if (MediaWikiClient::getArticleTitle('http://en.wikipedia.org/wiki/C%2B%2B') != 'C++') echo "FAIL 1\n";

if (!is_mediawiki_url('http://sv.wiktionary.org/wiki/bestick'))   echo "FAIL 10\n";
if (!is_mediawiki_url('https://en.wikipedia.org/wiki/Cutlery'))   echo "FAIL 11\n";
if ( is_mediawiki_url('http://en.wikipedia.org/wwapw'))           echo "FAIL 12\n";
if ( is_mediawiki_url('http://www.www.com/wwapw'))                echo "FAIL 13\n";

?>
