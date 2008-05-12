<?php

require_once('config_required.php');

$session->requireLocalhost();

echo '<h1>Create config.php file</h1>';

echo 'I found the following compatible db extensions loaded in PHP:<br/>';
	
if (extension_loaded('mysqli')) {
	echo '<div class="okay">MySQLi extension (recommended, requires PHP 5)</div>';
}

if (extension_loaded('mysql')) {
	echo '<div class="critical">MySQL extension (works with PHP 4, use MySQLi instead if possible, better performance, more tested)</div>';
}

if (extension_loaded('pgsql')) {
	echo '<div class="critical">PostgreSQL extension (EXPERIMENTAL)</div>';
}
?>
