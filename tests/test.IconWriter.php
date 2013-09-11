<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IconWriter.php');


$f = '/devel/cs/savak.icn.se/favicon.png';

$out = 'favicon.ico';

$im = new IconWriter();
$im->addImage($f);
$im->write($out);

?>
