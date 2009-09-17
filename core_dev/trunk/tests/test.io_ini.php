<?php

require_once('/var/www/core_dev/trunk/core/io_ini.php');

$x = new ini('test.ini');

$x->set('Category', 'spex', 17 );

$val = $x->get('Category', 'spex');

echo "category->val: ".$val."\n";


$l = $x->getAsArray();
print_r($l);


?>
