<?php
/**
 * $Id$
 */

require_once('find_config.php');

$session->requireAdmin();

require('design_admin_head.php');

if (isset($_GET['gb'])) {
	echo '<h1>Incoming GUESTBOOK</h1>';

	$cnt = getGuestbookCount();
	echo $cnt;


} else {
	echo '<h1>Incoming objects</h1>';
	echo '<a href="?gb">GUESTBOOK</a><br/>';
	echo '<a href="?msg">MESSAGES</a><br/>';
	echo '<a href="?blog">BLOGS</a><br/>';
}

require('design_admin_foot.php');;
?>
