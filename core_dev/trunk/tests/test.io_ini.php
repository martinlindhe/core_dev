<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('io_ini.php');

$x = new ini('test.ini');

$x->set('Category', 'spex', 17 );
$val = $x->get('Category', 'spex');

if ($val != 17) echo "FAIL 1\n";

?>
