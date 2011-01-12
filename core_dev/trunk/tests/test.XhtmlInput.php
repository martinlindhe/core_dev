<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('XhtmlComponentInput.php');

$input = new XhtmlComponentInput();
$input->name  = "hej";
$input->value = 555;
$input->size  = 10;

echo $input->render();

?>
