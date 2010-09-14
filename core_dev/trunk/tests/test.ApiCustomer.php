<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SqlHandler.php');
require_once('sql_mysql.php');

require_once('ApiCustomer.php');
require_once('ApiCustomerList.php');

$x = new ApiCustomer('PHONECAFE');

echo $x->getName()."\n";
echo $x->getId()."\n";

//$x->setSetting('currency','SEK');

echo $x->getSetting('currency')."\n";

?>
