<?php

$path = '/var/www/process/';
if (!file_exists($path.'config.php')) {
	$path = '/home/ml/dev/process/';
}

require_once($path.'config.php');

?>
