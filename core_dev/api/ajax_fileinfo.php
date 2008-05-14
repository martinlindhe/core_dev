<?php

/**
 * $Id$
 *
 * Returns details about a certain file, in HTML format for inclusion in a div element
 */

require_once('find_config.php');

if ((!$session->id && !$files->anon_uploads) || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

showFileInfo($_GET['i']);
?>
