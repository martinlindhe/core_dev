<?php

/**
 * $Id$
 */

if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

require_once('find_config.php');

createXHTMLHeader();
	
echo '<body style="background-color: #D80911;">';
echo '<center>';
showComments(COMMENT_FILE, $_GET['i'], 20, 3);
echo '</center>';
?>
