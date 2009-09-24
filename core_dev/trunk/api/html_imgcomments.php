<?php

/**
 * $Id$
 */

if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

require_once('find_config.php');

$header = new xhtml_header();
echo $header->render();

echo '<body style="background-color: #D80911;">';
echo '<center>';
echo showComments(COMMENT_FILE, $_GET['i'], 20, 3);
echo '</center>';
?>
