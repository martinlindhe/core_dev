<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('DifferenceEngine.php');

$x1 = "din mamma heter kallops\nDin med!\n";
$x2 = "min mamma heter kallops\nDin med!\n";

$df  = new Diff( $x1, $x2 );

// $form = new TableDiffFormatter();
$form = new UnifiedDiffFormatter();

echo $form->format($df);

?>
