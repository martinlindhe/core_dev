<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('client_shorturl.php');

$url = 'http://www.dn.se/kultur-noje/musik/musikbranschen-varnas-for-varningsbrev-1.881738';
$url = 'http://developer.yahoo.com/yui/editor/';
$s = new shorturl(shorturl::TR_IM);
echo $s->getShortUrl($url)."\n";

?>
