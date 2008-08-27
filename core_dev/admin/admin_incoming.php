<?php
/**
 * $Id$
 */

require_once('find_config.php');

$session->requireAdmin();

require('design_admin_head.php');

function adminGbRow($row, $i)
{
	$out  = '<tr style="background-color:'.($i%2 ? '#eee' : '#aaa').'"><td>';
	$out .= Users::link($row['authorId'], $row['authorName']).' at '.formatTime($row['timeCreated']).'<br/>';
	if ($row['subject']) $out .= '<b>'.$row['subject'].'</b><br/>';
	$out .= nl2br($row['body']);
	$out .= '</td></tr>';

	return $out;
}

if (isset($_GET['gb'])) {
	echo '<h1>Incoming GUESTBOOK</h1>';

	$tot_cnt = getGuestbookCount();
	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getGuestbookItems(0, $pager['limit']);

	echo xhtmlTable($list, '', 'adminGbRow');

} else {
	echo '<h1>Incoming objects</h1>';
	echo '<a href="?gb">GUESTBOOK</a><br/>';
	echo '<a href="?msg">MESSAGES</a><br/>';
	echo '<a href="?blog">BLOGS</a><br/>';
}

require('design_admin_foot.php');;
?>
