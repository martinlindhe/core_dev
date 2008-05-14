<?php

/**
 * $Id$
 *
 * Moves a file
 *
 * $_GET['i'] file id
 * $_GET['c'] category id to move file to
 */

require_once('find_config.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" ?>';

if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['c']) || !is_numeric($_GET['c'])) die('<bad/>');

$files->moveFile($_GET['c'], $_GET['i']);

echo '<ok/>';
?>
