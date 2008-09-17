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

	//$out  = '<tr class="'.($i%2 ? 'gb_row_even' : 'gb_row_odd').'">';
	$out  = '<tr>';
	$out .= '<td>'.Users::linkThumb($row['authorId'], $row['authorName']).'</td>';
	$out .= '<td>'.Users::link($row['authorId'], $row['authorName']).' wrote at '.formatTime($row['timeCreated']).' to '.Users::link($row['userId']).'<br/>';
	if ($row['subject']) $out .= '<b>'.$row['subject'].'</b><br/>';
	$out .= formatUserInputText($row['body']);
	if ($session->isAdmin) {
		$out .= '<br/>'.coreButton('Delete', '?gb&gbremove='.$row['entryId']);
	}
	if ($session->isAdmin || $session->id == $row['authorId'] || $session->id == $row['userId']) {
		//FIXME show history between these two users
		//$out .= '<a href="">History</a>';
	}
	$out .= '<br/><br/></td></tr>';

	return $out;
}

function messageRow($row, $i)
{
	global $session;

	if ($session->isAdmin && !empty($_GET['msgremove']) && $_GET['msgremove'] == $row['msgId']) {
		markMessageDeleted($row['msgId']);
		$i--;
		return;
	}

	//$out  = '<tr class="'.($i%2 ? 'gb_row_even' : 'gb_row_odd').'">';
	$out  = '<tr>';
	$out .= '<td>'.Users::linkThumb($row['fromId']).'</td>';
	$out .= '<td>'.Users::link($row['fromId']).' wrote at '.formatTime($row['timeCreated']).' to '.Users::link($row['toId']).'<br/>';
	if ($row['subject']) $out .= '<b>'.$row['subject'].'</b><br/>';
	$out .= formatUserInputText($row['body']).'<br/>';
	if ($row['timeRead']) {
		$out .= '<i>Message read '.formatTime($row['timeRead']).'</i>';
	} else {
		$out .= '<b>Message is unread</b>';
	}

	if ($session->isAdmin) {
		$out .= '<br/>'.coreButton('Delete', '?msg&msgremove='.$row['msgId']);
	}
	if ($session->isAdmin || $session->id == $row['fromId'] || $session->id == $row['toId']) {
		//FIXME show history between these two users
		//$out .= '<a href="">History</a>';
	}
	$out .= '<br/><br/></td></tr>';

	return $out;
}


function blogRow($row, $i)
{
	global $session;

	if ($session->isAdmin && !empty($_GET['blogremove']) && $_GET['blogremove'] == $row['blogId']) {
		deleteBlog($row['blogId']);
		$i--;
		return;
	}
	//$out  = '<tr class="'.($i%2 ? 'gb_row_even' : 'gb_row_odd').'">';
	$out  = '<tr>';
	$out .= '<td>'.Users::linkThumb($row['userId']).'</td>';
	$out .= '<td>'.Users::link($row['userId']).' wrote at '.formatTime($row['timeCreated']).'<br/>';
	if ($row['subject']) $out .= '<b>'.$row['subject'].'</b><br/>';
	$out .= formatUserInputText($row['body']).'<br/>';
	if ($row['isPrivate']) {
		$out .= '<b>PRIVATE BLOG!</b><br/>';
	}

	if ($session->isAdmin) {
		$out .= '<br/>'.coreButton('Delete', '?blog&blogremove='.$row['blogId']);
	}
	$out .= '<br/><br/></td></tr>';

	return $out;
}

if (isset($_GET['gb'])) {
	echo '<h1>Incoming GUESTBOOK ENTRIES</h1>';

	$tot_cnt = getGuestbookCount();
	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getGuestbookItems(0, $pager['limit']);
	echo xhtmlTable($list, '', 'guestbookRow');

} else if (isset($_GET['msg'])) {
	echo '<h1>Incoming MESSAGES</h1>';

	$tot_cnt = getMessagesCount();
	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getMessages(0, 0, $pager['limit']);
	echo xhtmlTable($list, '', 'messageRow');

} else if (isset($_GET['blog'])) {
	echo '<h1>Incoming BLOGS</h1>';

	$tot_cnt = getBlogCount();
	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getBlogs(0, $pager['limit']);
	echo xhtmlTable($list, '', 'blogRow');

} else if (isset($_GET['profimg'])) {
	echo '<h1>Incoming PROFILE IMAGES</h1>';

	$tot_cnt = getAllUserdataSettingsCount(USERDATA_TYPE_IMAGE);

	$pager = makePager($tot_cnt, 10);

	echo $pager['head'];

	$list = getAllUserdataSettings(USERDATA_TYPE_IMAGE, $pager['limit']);

	foreach ($list as $row) {
		echo showThumb($row['settingValue'], '', 270, 200);
		if (isInQueue($row['settingValue'], MODERATION_PRES_IMAGE)) {
			echo 'IN MODERATION QUEUE!<br/>';
		}
	}


} else {
	echo '<h1>Incoming objects</h1>';
	echo '<a href="?gb">GUESTBOOK</a><br/>';
	echo '<a href="?msg">MESSAGES</a><br/>';
	echo '<a href="?blog">BLOGS</a><br/>';
	echo '<a href="?profimg">PROFILE IMAGES</a><br/>';
}

require('design_admin_foot.php');;
?>