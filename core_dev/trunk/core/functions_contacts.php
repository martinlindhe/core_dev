<?php
/**
 * $Id$
 *
 * Implements friend lists. also implements blocked contacts
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//TODO: rename to atom_contacts.php

define('CONTACT_FRIEND',	1);
define('CONTACT_BLOCKED',	2);

$config['contacts']['friend_requests'] = true; //sends a request to another user to become friends,if false, it simply adds other user to your contact list

/**
 * Checks if $userId has a contact of type $_type with $otherId
 *
 * @param $_type type of contact (friend or blocked)
 * @param $userId user id
 * @param $otherId user id of other person
 * @return true if userId has otherId as contact
 */
function haveContact($_type, $userId, $otherId)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($userId) || !is_numeric($otherId)) return false;

	$q = 'SELECT contactId FROM tblContacts WHERE userId='.$userId.' AND otherUserId='.$otherId.' AND contactType='.$_type;
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * Returns TRUE if you are friend with specified user
 */
function isFriends($userId)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($userId)) return false;

	$q = 'SELECT contactId FROM tblContacts WHERE contactType='.CONTACT_FRIEND.' AND userId='.$userId.' AND otherUserId='.$h->session->id;
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * Checks if userId has blocked $otherId
 *
 * @param $userId user id
 * @param $otherId user id
 * @return true if $userId has blocked user $otherId
 */
function isUserBlocked($userId, $otherId)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($otherId)) return false;

	$q = 'SELECT contactId FROM tblContacts WHERE userId='.$userId.' AND otherUserId='.$otherId.' AND contactType='.CONTACT_BLOCKED;
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * Deletes a contact entry
 *
 * @param $_type type of contact (friend, blocked)
 * @param $otherId user Id to remove contact entry with (session->id is the affected user)
 * @return true on success
 */
function removeContact($_type, $otherId)	//FIXME rename to deleteContact()
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($_type) || !is_numeric($otherId)) return false;

	$q = 'DELETE FROM tblContacts WHERE userId='.$h->session->id.' AND otherUserId='.$otherId.' AND contactType='.$_type;
	$q2 = 'DELETE FROM tblContacts WHERE userId='.$otherId.' AND otherUserId='.$h->session->id.' AND contactType='.$_type;

	if ($_type == CONTACT_BLOCKED) {
		if ($db->delete($q)) return true;
	} else {
		if ($db->delete($q) && $db->delete($q2)) return true;
	}
	return false;
}

/**
 * Adds or updates a user contact (relation with another user)
 *
 * @param $_type type of contact (friend, blocked)
 * @param $userId user id
 * @param $otherId user id
 * @param $groupId contact group id
 */
function setContact($_type, $userId, $otherId, $groupId = 0)
{
	global $db;
	if ($userId == $otherId || !is_numeric($_type) || !is_numeric($userId) || !is_numeric($otherId) || !is_numeric($groupId)) return false;

	if (!haveContact($_type, $userId, $otherId)) {
		//Create new contact
		$q = 'INSERT INTO tblContacts SET userId='.$userId.',contactType='.$_type.',otherUserId='.$otherId.',groupId='.$groupId.',timeCreated=NOW()';
		$db->insert($q);
	} else {
		//Change the contact group
		$q = 'UPDATE tblContacts SET groupId='.$groupId.' WHERE userId='.$userId.' AND contactType='.$_type.' AND otherUserId='.$otherId;
		$db->update($q);
	}
}

/**
 * Returns one type of contacts for specified userId.
 *
 * @param $_type type of contact (friend, blocked)
 * @param $userId user id
 * @param $groupId contact group id
 */
function getContacts($_type, $userId, $groupId = '', $_limit_sql = '')
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($userId)) return false;
	//FIXME returnera namn på gruppen som kontakten tillhör "Gammalt ex", "Suparpolare" etc
	//FIXME $groupId ignoreras

	$q  = 'SELECT t1.*,t2.userName,t2.timeLastActive ';
	$q .= 'FROM tblContacts AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t2.userId = t1.otherUserId) ';
	$q .= 'WHERE t1.userId='.$userId.' AND t1.contactType='.$_type.' ';
	$q .= 'ORDER BY t2.userName ASC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Returns one type of contacts count for specified userId.
 *
 * @param $_type type of contact (friend, blocked)
 * @param $userId user id
 * @param $groupId contact group id
 */
function getContactsCount($_type, $userId, $groupId = '')
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($userId)) return false;
	//FIXME returnera namn på gruppen som kontakten tillhör "Gammalt ex", "Suparpolare" etc
	//FIXME $groupId ignoreras

	$q  = 'SELECT count(t1.contactId) as cnt ';
	$q .= 'FROM tblContacts AS t1 ';
	$q .= 'WHERE t1.userId='.$userId.' AND t1.contactType='.$_type.' ';
	return $db->getOneItem($q);
}

/**
 * Returns one type of contacts for specified userId. Either their friend list or block list
 *
 * @param $_type type of contact (friend, blocked)
 * @param $userId user id
 * @param $groupId contact group id
 */
function getContactsWithMe($_type, $userId, $groupId = '')
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($userId)) return false;
	//FIXME returnera namn på gruppen som kontakten tillhör "Gammalt ex", "Suparpolare" etc
	//FIXME $groupId ignoreras

	$q  = 'SELECT t1.*,t2.userName,t2.timeLastActive ';
	$q .= 'FROM tblContacts AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t2.userId = t1.otherUserId) ';
	$q .= 'WHERE t1.otherUserId='.$userId.' AND t1.contactType='.$_type.' ';
	$q .= 'ORDER BY t2.userName ASC';
	return $db->getArray($q);
}

/**
 * Deletes all contacts of $_type for specified user
 *
 * @param $_type type of contacts (friends / blocked users)
 * @param $userId user id
 * @return number of contacts removed
 */
function deleteContacts($_type, $userId)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($userId)) return false;

	$q = 'DELETE FROM tblContacts WHERE userId='.$userId.' AND contactType='.$_type;
	return $db->delete($q);
}

/**
 * Deletes all contacts for specified user
 *
 * @param $_type type of contacts (friends / blocked users)
 * @param $userId user id
 * @return number of contacts removed
 */
function deleteAllContacts($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'DELETE FROM tblContacts WHERE userId='.$userId;
	return $db->delete($q);
}

/**
 * Returns an array with $userId's all friends, including usernames & "isOnline" boolean, but no other info
 *
 * @param $_type type of contacts (friends / blocked users)
 * @param $userId user id
 * @return array of contacts
 */
function getContactsFlat($_type, $userId)
{
	global $h, $db;
	if (!is_numeric($_type) || !is_numeric($userId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS contactName,';
	$q .= '(SELECT timeLastActive>=DATE_SUB(NOW(),INTERVAL '.$h->session->online_timeout.' SECOND)) AS isOnline ';
	$q .= 'FROM tblContacts AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t2.userId = t1.otherUserId) ';
	$q .= 'WHERE t1.userId='.$userId.' AND t1.contactType='.$_type.' ';
	$q .= 'ORDER BY t2.userName ASC';
	return $db->getArray($q);
}

/**
 * Adds a request-to-become-friends to $userId, from current user, with the optional relation category type
 *
 * @param $userId user id
 * @param $categoryId contact group id
 * @param $msg optional relation request message
 * @return requestId on success
 */
function addFriendRequest($userId, $categoryId, $msg = '')
{
	global $h, $db;

	if (!$h->session->id || !is_numeric($userId) || !is_numeric($categoryId) || haveContact(CONTACT_FRIEND, $h->session->id, $userId)) return false;

	$q = 'SELECT COUNT(reqId) FROM tblFriendRequests WHERE senderId='.$h->session->id.' AND recieverId='.$userId;
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblFriendRequests SET senderId='.$h->session->id.',recieverId='.$userId.',timeCreated=NOW(),categoryId='.$categoryId.',msg="'.$db->escape($msg).'"';
	return $db->insert($q);
}

/**
 * Returns all pending requests sent from current user
 *
 * @return array of pending requests
 */
function getSentFriendRequests()
{
	global $h, $db;

	$q  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
	$q .= ' WHERE t1.senderId='.$h->session->id;
	$q .= ' ORDER BY t1.timeCreated DESC';
	return $db->getArray($q);
}

/**
 * Returns all pending requests sent to $userId
 *
 * @return array of pending requests
 */
function getRecievedFriendRequests()
{
	global $h, $db;

	$q  = 'SELECT t1.*,t2.userName AS senderName,t3.categoryName FROM tblFriendRequests AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.senderId=t2.userId)';
	$q .= ' LEFT JOIN tblCategories AS t3 ON (t1.categoryId=t3.categoryId)';
	$q .= ' WHERE t1.recieverId='.$h->session->id;
	$q .= ' ORDER BY t1.timeCreated DESC';
	return $db->getArray($q);
}

/**
 * Returns a specific friend request
 *
 * @param $requestId request Id
 * @return data for specified friend request
 */
function getFriendRequest($requestId)
{
	global $h, $db;

	if (!$h->session->id || !is_numeric($requestId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
	$q .= ' INNER JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
	$q .= ' WHERE t1.reqId='.$requestId;
	$q .= ' AND (t1.senderId='.$h->session->id.' OR t1.recieverId='.$h->session->id.')';
	return $db->getOneRow($q);
}

/**
 * Deletes a friend request, only doable for the person who created the request
 *
 * @param $otherId userid
 * @return true on success
 */
function removeSentFriendRequest($otherId)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($otherId)) return false;

	$q  = 'DELETE FROM tblFriendRequests';
	$q .= ' WHERE recieverId='.$otherId.' AND senderId='.$h->session->id;
	if ($db->delete($q)) return true;
	return false;
}

/**
 * Deletes a friend request, only doable for the person who recieved the request
 *
 * @param $otherId user id
 * @return true on success
 */
function denyFriendRequest($otherId)
{
	global $h, $db, $config;
	if (!$h->session->id || !is_numeric($otherId)) return false;

	$q  = 'DELETE FROM tblFriendRequests';
	$q .= ' WHERE senderId='.$otherId.' AND recieverId='.$h->session->id;
	$db->delete($q);

	//tell the request sender that the request was denied
	$msg = Users::link($h->session->id).' denied your friend request.';
	systemMessage($otherId, 'Denied friend request', $msg);
	return true;
}

/**
 * Deletes a friend request & creates a relation, only doable for the person who recieved the request
 *
 * @param $otherId user id
 * @return true on success
 */
function acceptFriendRequest($otherId)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($otherId)) return false;

	$q  = 'SELECT categoryId FROM tblFriendRequests  WHERE ';
	$q .= 'senderId='.$otherId.' AND recieverId='.$h->session->id.' LIMIT 1';
	$category = $db->getOneItem($q);

	$q  = 'DELETE FROM tblFriendRequests';
	$q .= ' WHERE senderId='.$otherId.' AND recieverId='.$h->session->id;
	$cnt = $db->delete($q);

	if ($cnt != 1) return false;

	//create a friend relation
	setContact(CONTACT_FRIEND, $h->session->id, $otherId, $category);
	setContact(CONTACT_FRIEND, $otherId, $h->session->id, $category);

	//tell the request sender that the request was accepted
	$msg = Users::link($h->session->id).' accepted your friend request, and has been added to your contact list.';
	systemMessage($otherId, 'Accepted friend request', $msg);
	return true;
}

/**
 * Adds a block between two users, both users block eachother
 *
 * @param $otherId user id
 * @return true on success
 */
function addContactBlock($otherId)
{
	global $h;
	if (!$h->session->id || !is_numeric($otherId)) return false;

	//create a block
	setContact(CONTACT_BLOCKED, $h->session->id, $otherId);

	//tell the request sender that the request was accepted
	$msg = Users::link($h->session->id).t(' has blocked you.');
	systemMessage($otherId, t('User blocking'), $msg);
	return true;
}

/**
 * Returns true if current user has a pending friend request with $userId
 *
 * @param $userId
 * @return true or false
 */
function hasPendingFriendRequest($userId)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($userId)) return false;

	$q  = 'SELECT reqId FROM tblFriendRequests ';
	$q .= 'WHERE senderId='.$h->session->id.' AND recieverId='.$userId;
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * Displays current user's friend list
 */
function displayFriendList()
{
	global $h, $db, $config;

	$userId = $h->session->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $userId = $_GET['id'];

	if ($h->session->id != $userId && isset($_GET['addfriend'])) {
		if ($config['contacts']['friend_requests']) {

			if (!empty($_POST['type_id'])) {
				//sends a request to create a contact to user
				addFriendRequest($userId, $_POST['type_id'], $_POST['msg']);
				echo 'A request has been sent to the user to create a contact<br/>';
				echo 'You will recieve a message when the user responds to the request.<br/><br/>';
				return;
			}
			echo '<h1>Send friend request</h1>';
			echo '<form method="post" action="">';
			echo 'So you wish to send a friend request to '.Users::link($userId).'?<br/>';
			echo 'First, you need to choose relation type: ';
			echo xhtmlSelectCategory(CATEGORY_CONTACT, 0, 'type_id').'<br/>';
			echo '(Optional) send a message:<br/>';
			echo '<textarea name="msg" cols="40" rows="6"></textarea><br/>';
			echo '<input type="submit" class="button" value="Send request"/>';
			echo '</form>';
			return;
		} else {
			//directly add contact to own contact list, dont send request
			setContact(CONTACT_FRIEND, $h->session->id, $userId);
		}
	}

	if ($h->session->id != $userId) {
		if (isset($_GET['removefriend'])) removeContact(CONTACT_FRIEND, $userId);

		if (hasPendingFriendRequest($userId)) {
			echo '<div class="item">';
			echo 'You already have a pending relation request with this user.<br/><br/>';
			echo 'You can remove your pending relation requests by clicking <a href="'.$_SERVER['PHP_SELF'].'?request_stopwait='.$userId.'">here</a>.';
			echo '</div><br/>';
		} else {
			if (!haveContact(CONTACT_FRIEND, $h->session->id, $userId)) {
				echo '<a href="?id='.$userId.'&amp;addfriend">Become friends</a><br/>';
			} else {
				echo '<a href="?id='.$userId.'&amp;removefriend">Remove friend contact</a><br/>';
			}
		}
		return;
	}

	if ($userId == $h->session->id) {
		if (!empty($_GET['request_stopwait'])) {
			removeSentFriendRequest($_GET['request_stopwait']);
		}

		if (isset($_GET['request_deny'])) {
			denyFriendRequest($_GET['request_deny']);
		}

		if (isset($_GET['request_accept'])) {
			acceptFriendRequest($_GET['request_accept']);
		}

		$list = getSentFriendRequests();
		if (count($list)) {
			echo 'Your sent friend requests:<br/>';

			foreach ($list as $row) {
				echo '<div class="item">';
				echo Users::link($row['recieverId'], $row['recieverName']).' - ';
				echo '<a href="?request_stopwait='.$row['recieverId'].'">Remove</a><br/>';
				echo '</div><br/>';
			}
		}

		$list = getRecievedFriendRequests();
		if (count($list)) {
			echo 'Your recieved friend requests:<br/>';
			foreach ($list as $row) {
				echo '<div class="item">';
				echo Users::link($row['senderId'], $row['senderName']).' wants to be '.$row['categoryName'].' - Do you ';
				echo '<a href="?request_accept='.$row['senderId'].'">Accept</a> or ';
				echo '<a href="?request_deny='.$row['senderId'].'">Deny</a>?<br/>';
				if ($row['msg']) {
					echo 'Personal message: '.nl2br($row['msg']);
				}
				echo '</div><br/>';
			}
		}
	}

	$list = getContactsFlat(CONTACT_FRIEND, $userId);

	if ($h->session->id != $userId) {
		echo 'Friends:'.Users::getName($userId).'<br/>';
	} else {
		echo 'Your friend list:<br/>';
	}

	if (!count($list)) {
		echo 'No friends.';
		return;
	}

	foreach ($list as $row) {
		echo '<div class="'.($row['isOnline']?'friend_online':'friend_offline').'">';

		echo Users::link($row['otherUserId'], $row['contactName']);

		echo '<a href="messages.php?id='.$row['contactId'].'"><img src="'.coredev_webroot().'gfx/icon_mail.png" alt="Send a message to '.$row['contactName'].'"/></a>';
		echo '</div>';
	}
}
?>
