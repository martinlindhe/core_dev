<?php

/**
 * $Id$
 *
 * Passes thru a file to the client, setting the proper mime type (pdf, wmv, etc inside browser)
 */

//FIXME: kill this file, it does the same as file.php

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

require_once('find_config.php');

$files->sendFile($_GET['id'], true);
?>
