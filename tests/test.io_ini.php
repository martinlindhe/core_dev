<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('io_ini.php');


$ini_file = 'test.ini';

$x = new ini($ini_file);

$x->set('Category', 'spex', 17 );
$val = $x->get('Category', 'spex');

if ($val != 17) echo "FAIL 1\n";

unlink($ini_file);

?>
