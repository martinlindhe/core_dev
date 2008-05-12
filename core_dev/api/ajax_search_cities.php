<?php

/**
 * $Id$
 *
 * Returns XHTML block for city selection
 */

require_once('find_config.php');

if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

echo ZipLocation::citySelect($_GET['i']);
?>
