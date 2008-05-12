<?php

/**
 * $Id$
 *
 * Deletes a file
 */

require_once('find_config.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" ?>';

if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');
if ($files->getOwner($_GET['i']) != $session->id) die('<bad2/>');

$files->deleteFile($_GET['i']);

echo '<ok/>';
?>
