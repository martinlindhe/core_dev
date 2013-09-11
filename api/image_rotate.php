<?php

/**
 * $Id$
 *
 * i - file id
 * a - angle to rotate
 */

require_once('find_config.php');

if (!$h->session->id || empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['a']) || !is_numeric($_GET['a'])) die;

$_angle = $_GET['a'];
if ($_angle != 90 && $_angle != -90) die('y');

$h->files->imageRotate($_GET['i'], $_angle);
?>
