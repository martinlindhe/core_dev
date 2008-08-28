<?php
/**
 * $Id$
 */

require_once('find_config.php');

$session->requireAdmin();

require('design_admin_head.php');

function guestbookRow($row, $i)
{
	global $session;
	if ($session->isAdmin && !empty($_GET['gbremove']) && $_GET['gbremove'] == $row['entryId']) {
		removeGuestbookEntry($row['entryId']);
		$i--;
		return;
	}

	$out  = '<tr class="'.($i%2 ? 'gb_row_even' : 'gb_row_odd').'"><td width="300">';
	$out .= Users::link($row['authorId'], $row['authorName']).' wrote to '.Users::link($row['userId']).' at '.formatTime($row['timeCreated']).'<br/>';
	if ($row['subject']) $out .= '<b>'.$row['subject'].'</b><br/>';
	$out .= formatUserInputText($row['body']);
	if ($session->isAdmin) {
		$out .= '<br/>'.coreButton('Delete', '?gb&gbremove='.$row['entryId']);
	}
	$out .= '</td></tr>';

	return $out;
}

if (isset($_GET['gb'])) {
	echo '<h1>Incoming GUESTBOOK</h1>';

	$tot_cnt = getGuestbookCount();
	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getGuestbookItems(0, $pager['limit']);

	echo xhtmlTable($list, '', 'guestbookRow');

} else {
	echo '<h1>Incoming objects</h1>';
	echo '<a href="?gb">GUESTBOOK</a><br/>';
	echo '<a href="?msg">MESSAGES</a><br/>';
	echo '<a href="?blog">BLOGS</a><br/>';
}

require('design_admin_foot.php');;
?>
