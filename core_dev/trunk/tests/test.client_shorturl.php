<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('client_shorturl.php');

$url = 'http://developer.yahoo.com/yui/editor/';
$s = new shorturl(shorturl::TR_IM);

if ($s->getShortUrl($url) != 'http://tr.im/JHnf') echo "FAIL 1\n";

?>
