<?php

/**
 * $Id$
 *
 * i - file id
 * p - percent to resize, relative to orginal image dimensions
 */

require_once('find_config.php');

if (!$h->session->id || empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['p']) || !is_numeric($_GET['p'])) die;

$h->files->imageResize($_GET['i'], $_GET['p']);
?>
