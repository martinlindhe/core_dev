<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Diff.php');

$x1 = "din mamma heter kallops\nDin med!\n";
$x2 = "min mamma heter kallops\nDin med!\n";

$diff = new Diff_DEPRECATED($x1, $x2);

d( $diff->getDiff() );
?>
