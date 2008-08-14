<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_textformat.php'); //for formatting messages

define('MESSAGE_GROUP_INBOX',	1);
define('MESSAGE_GROUP_OUTBOX',	2);

function sendMessage($_id, $_subj, $_msg)
{
	global $db, $session;
	if (!is_numeric($_id)) return false;

	//Adds message to recievers inbox
	$q = 'INSERT INTO tblMessages SET ownerId='.$_id.',fromId='.$session->id.',toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_INBOX;
	$db->insert($q);
		
	//Add message to senders outbox
	$q = 'INSERT INTO tblMessages SET ownerId='.$session->id.',fromId='.$session->id.',toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_OUTBOX;
	$id = $db->insert($q);

	return $id;
}

/**
 * Sets the answerId
 */
function setMessageAnswerId($msgId, $answerId)
{
	global $db;
	
	if (!is_numeric($msgId) && !is_numeric($answerId)) return false;
	
	$q = 'UPDATE tblMessages SET answerId='.$answerId.' WHERE msgId = '.$msgId.' LIMIT 1';
	return $db->update($q);
	
}


/**
 * Adds a system message to recievers inbox
 */
function systemMessage($_id, $_subj, $_msg)
{
	global $db;
	if (!is_numeric($_id)) return false;

	$q = 'INSERT INTO tblMessages SET ownerId='.$_id.',fromId=0,toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_INBOX;
	$db->insert($q);
	return true;
}

/**
 * Returns the number of items in the message search result
 */
function getMessageFreeTextSearchCount($text)
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT count(*) FROM tblMessages ';
	$q .= 'WHERE (body LIKE "%'.$text.'%" OR subject LIKE "%'.$text.'%")';
	return $db->getOneItem($q);
}

/**
 * Returns the message search result
 */
function getMessageFreeTextSearch($text, $_limit_sql = '')
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT t.*, u1.userName AS authorName, u2.userName AS userName ';
	$q .= 'FROM tblMessages t, tblUsers u1, tblUsers u2 WHERE t.fromId = ';
	$q .= 'u1.userId AND t.toId = u2.userId AND (t.body LIKE "%'.$text.'%" OR t.subject LIKE "%'.$text.'%") ';
	$q .= 'ORDER BY t.timeCreated DESC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Returns the number of items written in messages the specified date interval
 */
function getMessageCountPerDate($dateStart, $dateStop = '')
{
	global $db;

	if (empty($dateStop)) {
		$dateStop = $dateStart;
	}

	$q = 'SELECT floor(count(msgId)/2) AS cnt, date(timeCreated) AS date FROM tblMessages WHERE date(timeCreated) BETWEEN date("'.$dateStart.'") AND date("'.$dateStop.'") GROUP BY date(timeCreated)';
	return $db->getArray($q);
}

/**
 * XXX
 */
function getMessages($_group = 0, $_limit_sql = '')
{
	global $db, $session;
	if (!is_numeric($_group)) return false;

	switch ($_group) {
		case MESSAGE_GROUP_INBOX:
			$q  = 'SELECT t1.*,t1.fromId AS otherId, t2.userName AS otherName ';
			$q .= 'FROM tblMessages AS t1 ';
			$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.fromId=t2.userId) ';
			$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
			$q .= 'AND t1.timeDeleted IS NULL ';
			$q .= 'ORDER BY timeCreated DESC '.$_limit_sql;
			break;

		case MESSAGE_GROUP_OUTBOX:
			$q  = 'SELECT t1.*,t1.toId AS otherId, t2.userName AS otherName ';
			$q .= 'FROM tblMessages AS t1 ';
			$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.toId=t2.userId) ';
			$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
			$q .= 'AND t1.timeDeleted IS NULL ';
			$q .= 'ORDER BY timeCreated DESC'.$_limit_sql;
			break;
				
		default:
			$q = 'SELECT * FROM tblMessages WHERE ownerId='.$session->id.' AND groupId='.$_group;
	}

	return $db->getArray($q);
}

/**
 * XXX
 */
function getMessagesCount($_group = 0)
{
	global $db, $session;
	if (!is_numeric($_group)) return false;

	switch ($_group) {
		case MESSAGE_GROUP_INBOX:
			$q  = 'SELECT count(t1.fromId) AS cnt ';
			$q .= 'FROM tblMessages AS t1 ';
			$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
			$q .= 'AND t1.timeDeleted IS NULL';
			break;

		case MESSAGE_GROUP_OUTBOX:
			$q  = 'SELECT count(t1.toId) AS cnt ';
			$q .= 'FROM tblMessages AS t1 ';
			$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
			$q .= 'AND t1.timeDeleted IS NULL';
			break;
				
		default:
			$q = 'SELECT count(*) FROM tblMessages WHERE ownerId='.$session->id.' AND groupId='.$_group;
	}

	return $db->getOneItem($q);
}

/**
 * XXX
 */
function getMessagesNewItemsCount($userId, $_group = 0)
{
	global $db, $session;
	if (!is_numeric($_group)) return false;

	$q  = 'SELECT count(fromId) FROM tblMessages WHERE';
	$q .= ' ownerId='.$userId;
	if ($_group) $q .= ' AND groupId='.$_group;
	$q .= ' AND timeRead IS NULL';

	return $db->getOneItem($q);
}

/**
 * XXX
 */
function getMessage($_id)
{
	global $db, $session;
	if (!is_numeric($_id)) return false;

	$q = 'SELECT * FROM tblMessages WHERE ownerId='.$session->id.' AND msgId='.$_id.' AND timeDeleted IS NULL';
	$row = $db->getOneRow($q);
	if ($row['ownerId'] != $session->id) return false;
	return $row;
}

/**
 * Marks the message as read if its recipent & owner is current user.
 */
function markMessageRead($_id)
{
	global $db, $session;
	if (!is_numeric($_id)) return false;
		
	$q = 'UPDATE tblMessages SET timeRead=NOW() WHERE ownerId='.$session->id.' AND toId='.$session->id.' AND msgId='.$_id;
	return $db->update($q);
}

/**
 * Marks the message as deleted if its owner is current user.
 */
function markMessageDeleted($_id)
{
	global $db, $session;
	if (!is_numeric($_id)) return false;

	$q = 'UPDATE tblMessages SET timeDeleted=NOW() WHERE ownerId='.$session->id.' AND msgId='.$_id;
	return $db->update($q);
}

/**
 * XXX
 */
function showMessages($_group = 0)
{
	global $db, $session, $config;
	if (!is_numeric($_group)) return false;
		
	if (!empty($_GET['read']) && is_numeric($_GET['read'])) {
		//Shows one message
		$msg = getMessage($_GET['read']);
		if (!$msg) return false;

		echo '<div class="msg">';
		echo '<div class="msg_head">';
			echo ($msg['subject'] ? $msg['subject']:t('No subject')).' '.t('at').' '.$msg['timeCreated'].'<br/>';
			if ($msg['fromId']) {
				echo t('From').' '.Users::link($msg['fromId']).'<br/>';
			} else {
				echo '<b>'.t('System message').'</b><br/>';
			}
			echo t('To').' '.Users::link($msg['toId']).'<br/>';
			echo (!$msg['timeRead']?t('UNREAD'):t('READ'));
		echo '</div>';
		echo '<div class="msg_body">';
			echo formatUserInputText($msg['body']);
		echo '</div>';
		echo '</div>';

		markMessageRead($_GET['read']);

		if ($msg['fromId'] && $msg['fromId'] != $session->id) {
			echo ' <a href="messages.php?id='.$msg['fromId'].'&amp;r='.$msg['msgId'].'">'.t('Reply').'</a><br/>';
		}
		echo '<br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'">'.t('Return to message overview').'</a>';

		return true;
	}

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		markMessageDeleted($_GET['delete']);
	}

	if (!$_group && !empty($_GET['g']) && is_numeric($_GET['g'])) $_group = $_GET['g'];
	if (!$_group) $_group = MESSAGE_GROUP_INBOX;

	echo ($_group==MESSAGE_GROUP_INBOX?'<b>'.t('INBOX').'</b>':'<a href="?g='.MESSAGE_GROUP_INBOX.'">'.t('INBOX').'</a>').' | ';
	echo ($_group==MESSAGE_GROUP_OUTBOX?'<b>'.t('OUTBOX').'</b>':'<a href="?g='.MESSAGE_GROUP_OUTBOX.'">'.t('OUTBOX').'</a>').'<br/>';
	echo '<br/>';

	$list = getMessages($_group);
	if (!$list) {
		switch ($_group) {
			case MESSAGE_GROUP_INBOX: echo t('No messages in inbox'); break;
			case MESSAGE_GROUP_OUTBOX: echo t('No messages in outbox'); break;
			default: echo t('No messages'); break;
		}
		return false;
	}

	$i=0;
	echo '<table>';
	foreach ($list as $row) {
		echo '<tr>';
		echo '<td width="200">';
		echo '<a href= "'.$_SERVER['PHP_SELF'].'?read='.$row['msgId'].'">';
		echo ($row['subject']?$row['subject']:'no subject');
		echo '</a>';
		echo '</td>';

		echo '<td width="100">'.Users::link($row['otherId'], $row['otherName']).'</td>';
		echo '<td width="140">'.$row['timeCreated'].'</td>';
		//(!$row['timeRead']?t('UNREAD'):t('READ')).'<br/>';
		echo '<td><a href="?g='.$_group.'&amp;delete='.$row['msgId'].'"><img src="'.$config['core']['web_root'].'gfx/icon_delete.png" alt="'.t('Delete').'" border="0"/></a></td>';
		echo '</tr>';
	}
	echo '</table>';

	return true;
}
?>
