<?php

$path = '/var/www/process/';
if (!is_dir($path)) {
	$path = '/home/ml/dev/projects/process/';
}

$config['no_session'] = true;
require_once($path.'config.php');

?>
