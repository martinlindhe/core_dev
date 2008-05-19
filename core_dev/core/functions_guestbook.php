<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('atom_moderation.php');	//for moderation functionality

/**
 * 
 */
function addGuestbookEntry($ownerId, $subject, $body)
{
	global $db, $session;
	if (!$session->id || !is_numeric($ownerId)) return false;

	//Strip all html
	$subject = $db->escape(strip_tags($subject));
	$body    = $db->escape(strip_tags($body));
	if (!$body) return false;

	/*
		spam / repeated message protection
		checks if the user has written a identical message in the last 5 minutes
	*/
	$q  = 'SELECT COUNT(*) FROM tblGuestbooks WHERE userId='.$ownerId.' AND authorId='.$session->id;
	$q .= ' AND subject="'.$subject.'" AND body="'.$body.'" AND timeCreated>=DATE_SUB(NOW(),INTERVAL 5 MINUTE)';
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblGuestbooks SET userId='.$ownerId.',authorId='.$session->id.',subject="'.$subject.'",body="'.$body.'",timeCreated=NOW()';
	$entryId = $db->insert($q);

	//Add entry to moderation queue
	if (isSensitive($subject) || isSensitive($body)) addToModerationQueue(MODERATION_GUESTBOOK, $entryId, true);
}

function removeGuestbookEntry($entryId)
{
	global $db;
	if (!is_numeric($entryId)) return false;

	//FIXME: deletedby, så admin-deletes syns
	$q = 'UPDATE tblGuestbooks SET entryDeleted=1,timeDeleted=NOW() WHERE entryId='.$entryId;
	$db->update($q);
}

/**
 * Return $userId's guestbook entries
 */
function getGuestbook($userId, $_limit_sql = '')
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS authorName ';
	$q .= 'FROM tblGuestbooks AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
	$q .= 'WHERE t1.userId='.$userId.' ';
	$q .= 'AND t1.entryDeleted=0 ';
	$q .= 'ORDER BY t1.timeCreated DESC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Return $userId's guestbook conversation with $otherId, $_limit_sql entries
 */
function getGuestbookConversation($userId, $otherId, $_limit_sql = '')
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($otherId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS authorName, t3.userName AS otherName ';
	$q .= 'FROM tblGuestbooks AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
	$q .= 'INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
	$q .= 'WHERE (t1.userId='.$userId.' OR t1.authorId = '.$userId.') AND ';
	$q .= '(t1.userId='.$otherId.' OR t1.authorId = '.$otherId.')';
	$q .= 'AND t1.entryDeleted=0 ';
	$q .= 'ORDER BY t1.timeCreated DESC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Returns $count last entries from $userId's guestbook
 */
function getGuestbookItems($userId, $count = 5)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($count)) return false;

	$q  = 'SELECT t1.*,t2.userName AS authorName ';
	$q .= 'FROM tblGuestbooks AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
	$q .= 'WHERE t1.userId='.$userId.' ';
	$q .= 'AND t1.entryDeleted=0 ';
	$q .= 'ORDER BY t1.timeCreated DESC';
	$q .= ' LIMIT 0,'.$count;
	return $db->getArray($q);
}

/**
 * Returns number of new entries in $userId's guestbook
 */
function getGuestbookNewItemsCount($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q  = 'SELECT count(entryId) FROM tblGuestbooks WHERE ';
	$q .= 'userId='.$userId.' AND entryDeleted=0 ';
	$q .= 'AND entryRead = 0';
	return $db->getOneItem($q);
}

/**
 * Returns one specific guestbook entry
 */
function getGuestbookItem($entryId)
{
	global $db;
	if (!is_numeric($entryId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS authorName,t3.userName FROM tblGuestbooks AS t1 ';
	$q .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
	$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
	$q .= 'WHERE entryId='.$entryId;
	return $db->getOneRow($q);
}

/**
 * Returns the number of items in the guestbook search result
 */
function getGuestbookFreeTextSearchCount($text)
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT count(*) FROM tblGuestbooks ';
	$q .= 'WHERE body LIKE "%'.$text.'%"';
	return $db->getOneItem($q);
}

/**
 * Returns the guestbook search result
 */
function getGuestbookFreeTextSearch($text, $_limit_sql = '')
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT t.*, u1.userName AS authorName, u2.userName AS userName ';
	$q .= 'FROM tblGuestbooks t, tblUsers u1, tblUsers u2 WHERE t.authorId = ';
	$q .= 'u1.userId AND t.userId = u2.userId AND t.body LIKE "%'.$text.'%" ';
	$q .= 'ORDER BY t.timeCreated DESC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Returns the number of items in the guestbook
 */
function getGuestbookCount($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryDeleted=0';
	return $db->getOneItem($q);
}

/**
 * Returns the number of items written in guestbooks the specified date interval
 */
function getGuestbookCountPerDate($dateStart, $dateStop = '')
{
	global $db;

	if (empty($dateStop)) $dateStop = $dateStart;

	$q = 'SELECT count(entryId) AS cnt, date(timeCreated) AS date FROM tblGuestbooks WHERE date(timeCreated) BETWEEN date("'.$dateStart.'") AND date("'.$dateStop.'") GROUP BY date(timeCreated)';
	return $db->getArray($q);
}

/**
 * Returns the number of items in the guestbook conversation
 */
function getGuestbookConversationCount($userId, $otherId)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($otherId)) return false;

	$q  = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE (userId='.$userId.' OR authorId='.$userId.') AND ';
	$q .= '(userId='.$otherId.' OR authorId='.$otherId.') AND entryDeleted=0';
	return $db->getOneItem($q);
}

/**
 * Returns the number of unread items in the guestbook
 */
function getGuestbookUnreadCount($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryRead=0';
	return $db->getOneItem($q);
}

/**
 * Marks current user's guestbook entries as read
 */
function markGuestbookRead()
{
	global $db, $session;
	if (!$session->id) return false;

	$q = 'UPDATE tblGuestbooks SET entryRead=1,timeRead=NOW() WHERE entryRead=0 AND userId='.$session->id;
	$db->update($q);
}

/**
 * Helper function to show a user's guestbook
 * FIXME: make it possible to override this function
 */
function showGuestbook($userId)
{
	global $config, $session;
	if ($session->isAdmin || $session->id == $userId) {
		if (!empty($_GET['remove'])) {
			removeGuestbookEntry($_GET['remove']);
		}
	}
	if ($session->id != $userId && !empty($_POST['body'])) {
		addGuestbookEntry($userId, '', $_POST['body']);
	}

	$historyId = 0;
	if (!empty($_GET['history']) && is_numeric($_GET['history'])) {
		$historyId = $_GET['history'];
		$tot_cnt = getGuestbookConversationCount($userId, $historyId);
	} else {
		$tot_cnt = getGuestbookCount($userId);
	}

	if (!$tot_cnt) {
		if ($session->id == $userId) {
			echo t('Your guestbook is empty');
		} else {
			echo t('The guestbook is empty');
		}
		echo '<br/><br/>';
	}

	if ($historyId) {
		echo t('Displaying guestbook history between yourself and').' '.Users::getName($historyId).'<br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$session->id.'">'.t('Return to guestbook overview').'</a><br/><br/>';
	}

	$pager = makePager($tot_cnt, 5);

	echo $pager['head'];
	if ($historyId) {
		$list = getGuestbookConversation($userId, $historyId, $pager['limit']);
	} else {
		$list = getGuestbook($userId, $pager['limit']);
	}
	foreach ($list as $row) {
		echo '<a name="gb'.$row['entryId'].'"></a>';
		echo '<div class="guestbook_entry">';

		echo '<div class="guestbook_entry_head">';
		echo t('From').' '.Users::link($row['authorId'], $row['authorName']);
		echo ', '.formatTime($row['timeCreated']);
		echo '</div>';

		if ($session->id == $userId) {
			if ($row['entryRead'] == 0) {
				echo '<img src="'.$config['core']['web_root'].'gfx/icon_mail.png" alt="'.t('Unread').'">';
			}
		}
		echo stripslashes($row['body']).'<br/>';

		if ($session->isAdmin || $session->id == $userId) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&amp;remove='.$row['entryId'].'">'.t('Remove').'</a>';
		}

		if ($session->id == $row['userId']) {
			echo ' | <a href="#" onclick="show_element_by_name(\'gb_reply_'.$row['entryId'].'\')">'.t('Reply').'</a>';
		}

		if (!$historyId && ($session->id == $row['authorId'] || $session->id == $row['userId'])) {
			echo ' | <a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&amp;history='.($row['authorId']==$session->id?$row['userId']:$row['authorId']).'">'.t('History').'</a>';
		}
		echo '</div><br/>';

		if ($session->id == $row['userId']) {
			echo '<div id="gb_reply_'.$row['entryId'].'" style="display:none;">';
			echo t('Reply to').' '.Users::getName($row['authorId']).':<br/>';
			echo '<form name="addGuestbook" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$row['authorId'].'">';
			echo '<textarea name="body" cols="40" rows="6"></textarea><br/>';
			echo '<input type="submit" class="button" value="'.t('Send reply').'"/>';
			echo '</form><br/>';
			echo '</div>';
		}
	}

	if ($session->id) {
		if ($session->id != $userId) {
			echo t('New entry').':<br/>';
			echo '<form name="addGuestbook" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$userId.'">';
			echo '<textarea name="body" cols="40" rows="6"></textarea><br/><br/>';
			echo '<input type="submit" class="button" value="'.t('Save').'"/>';
			echo '</form>';
		} else {
			//Mark all entries as read
			markGuestbookRead();
		}
	}
}

?>
