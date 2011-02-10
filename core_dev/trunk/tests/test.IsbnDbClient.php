<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IsbnDbClient.php');

$x = new IsbnDbClient();
$x->setApiKey('E2T62YHW');

$res = $x->getByISBN('978-0-552-77429-1');
d($res);

?>
