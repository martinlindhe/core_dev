<?php

/**
 * $Id$
 *
 * Reports back progress of current file upload to browser, using php_apc.dll extension
 */

if (empty($_GET['s']) || !is_numeric($_GET['s'])) die;

require_once('find_config.php');

$status = apc_fetch('upload_'.$_GET['s']);
print_r($status);
?>
