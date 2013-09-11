<?php

/**
 * $Id$
 *
 * Takes a file id, returns the file
 */

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

require_once('find_config.php');

$h->files->sendFile($_GET['id'], true);
?>
