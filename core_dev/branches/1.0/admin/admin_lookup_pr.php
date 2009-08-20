<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$h->session->requireAdmin();

$pr = $_POST['pr'];
$prData = getTodoItem($pr);
if ($prData) {
	header('Location: admin_todo_lists.php?id='.$prData['itemId']);
	die;
}

require('design_admin_head.php');

echo '<h1>Lookup PR</h1>';
echo 'PR '.$pr.' not found.<br/><br/>';
echo '<a href="admin_current_work.php">Go back to current work</a><br/>';

require('design_admin_foot.php');
?>
