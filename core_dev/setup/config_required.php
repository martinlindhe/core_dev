<?php

set_include_path('../core/');
require_once('class.Session.php');
restore_include_path();

$config['debug'] = true;
$config['session'] = array();
$session = new Session($config['session']);
?>
