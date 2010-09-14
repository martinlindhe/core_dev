<?php

require_once(dirname(__FILE__).'/../config.php');

require_once('ApiCustomer.php');

$x = new ApiCustomer('PHONECAFE');

echo $x->getName()."\n";
echo $x->getId()."\n";

//$x->setSetting('currency','SEK');

echo $x->getSetting('currency')."\n";

?>
