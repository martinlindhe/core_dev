<?
	//functions_contacts.php - implements friend lists. also implements blocked contacts

	define('CONTACT_FRIEND', 1);
	define('CONTACT_BLOCKED', 2);

	$config['contacts']['friend_requests'] = true; //sends a request to another user to become friends,if false, it simply adds other user to your contact list

	function haveContact($_type, $userId, $otherId)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($userId) || !is_numeric($otherId)) return false;

		$q = 'SELECT contactId FROM tblContacts WHERE userId='.$userId.' AND otherUserId='.$otherId.' AND contactType='.$_type;
		return $db->getOneItem($q);
	}

	/* returns true if $userId has blocked user $otherId */
	function isBlocked($userId, $otherId)
	{
		global $db;
		if (!is_numeric($userId) || !is_numeric($otherId)) return false;

		$q = 'SELECT contactId FROM tblContacts WHERE userId='.$userId.' AND otherUserId='.$otherId.' AND contactType='.CONTACT_BLOCKED;
		if ($db->getOneItem($q)) return true;
		return false;
	}

	function removeContact($_type, $otherId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($otherId)) return false;

		$q = 'DELETE FROM tblContacts WHERE userId='.$session->id.' AND otherUserId='.$otherId.' AND contactType='.$_type;
		$db->query($q);
	}

	/* Adds or updates a user contact (relation with another user) */
	function setContact($_type, $userId, $otherId, $groupId = 0)
	{
		global $db;
		if ($userId == $otherId || !is_numeric($_type) || !is_numeric($userId) || !is_numeric($otherId) || !is_numeric($groupId)) return false;

		if (!haveContact($_type, $userId, $otherId)) {
			/* Create new contact */
			$q = 'INSERT INTO tblContacts SET userId='.$userId.',contactType='.$_type.',otherUserId='.$otherId.',groupId='.$groupId.',timeCreated=NOW()';
			$db->insert($q);
		} else {
			/* Change the contact group */
			$q = 'UPDATE tblContacts SET groupId='.$groupId.' WHERE userId='.$userId.' AND contactType='.$_type.' AND otherUserId='.$otherId;
			$db->query($q);
		}
	}


	/* Returns one type of contacts for specified userId. Either their friend list or block list */
	function getContacts($_type, $userId, $groupId = '')
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($userId)) return false;
		//todo: returnera namn på gruppen som kontakten tillhör "Gammalt ex", "Suparpolare" etc

		$q  = 'SELECT t1.*,t2.userName,t2.timeLastActive ';
		$q .= 'FROM tblContacts AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t2.userId = t1.otherUserId) ';
		$q .= 'WHERE t1.userId='.$userId.' AND t1.contactType='.$_type.' ';
		$q .= 'ORDER BY t2.userName ASC';

		return $db->getArray($q);
	}

	/* Returns an array with $userId's all friends, including usernames & "isOnline" boolean, but no other info */
	function getContactsFlat($_type, $userId)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($userId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS contactName,';
		$q .= '(SELECT timeLastActive>=DATE_SUB(NOW(),INTERVAL 30 MINUTE)) AS isOnline ';
		$q .= 'FROM tblContacts AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t2.userId = t1.otherUserId) ';
		$q .= 'WHERE t1.userId='.$userId.' AND t1.contactType='.$_type.' ';
		$q .= 'ORDER BY t2.userName ASC';

		return $db->getArray($q);
	}

	function displayFriendList()
	{
		global $db, $session, $config;

		$userId = $session->id;
		if (!empty($_GET['id']) && is_numeric($_GET['id'])) $userId = $_GET['id'];

		if ($session->id != $userId && isset($_GET['addfriend'])) {
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
				echo 'So you wish to send a friend request to '.nameLink($userId).'?<br/>';
				echo 'First, you need to choose relation type: ';
				echo getCategoriesSelect(CATEGORY_CONTACT, 0, 'type_id').'<br/>';
				echo '(Optional) send a message:<br/>';
				echo '<textarea name="msg" cols="40" rows="6"></textarea><br/>';
				echo '<input type="submit" class="button" value="Send request"/>';
				echo '</form>';
				return;
			} else {
				//directly add contact to own contact list, dont send request
				setContact(CONTACT_FRIEND, $session->id, $userId);
			}
		}

		if ($session->id != $userId) {
			if (isset($_GET['removefriend'])) removeContact(CONTACT_FRIEND, $userId);

			if (hasPendingFriendRequest($userId)) {

				echo '<div class="item">';
				echo 'You already have a pending relation request with this user.<br/><br/>';
				echo 'You can remove your pending relation requests by clicking <a href="'.$_SERVER['PHP_SELF'].'?request_stopwait='.$userId.'">here</a>.';
				echo '</div><br/>';

			} else {
				if (!haveContact(CONTACT_FRIEND, $session->id, $userId)) {
					echo '<a href="?id='.$userId.'&amp;addfriend">Become friends</a><br/>';
				} else {
					echo '<a href="?id='.$userId.'&amp;removefriend">Remove friend contact</a><br/>';
				}
			}
			return;
		}

		if ($userId == $session->id) {
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
					echo nameLink($row['recieverId'], $row['recieverName']).' - ';
					echo '<a href="?request_stopwait='.$row['recieverId'].'">Remove</a><br/>';
					echo '</div><br/>';
				}
			}

			$list = getRecievedFriendRequests();
			if (count($list)) {
				echo 'Your recieved friend requests:<br/>';
				foreach ($list as $row) {
					echo '<div class="item">';
					echo nameLink($row['senderId'], $row['senderName']).' wants to be '.$row['categoryName'].' - Do you ';
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

		if ($session->id != $userId) {
			echo 'Friends:'.getUserName($userId).'<br/>';
		} else {
			echo 'Your friend list:<br/>';
		}

		if (!count($list)) {
			echo 'No friends.';
			return;
		}

		foreach ($list as $row) {
			echo '<div class="'.($row['isOnline']?'friend_online':'friend_offline').'">';

			echo nameLink($row['contactId'], $row['contactName']);

			echo '<a href="mess_new.php?id='.$row['contactId'].'"><img src="'.$config['core_web_root'].'gfx/icon_mail.png" alt="Send a message to '.$row['contactName'].'"/></a>';
			echo '</div>';
		}
	}

	/* Adds a request-to-become-friends to $userId, from current user, with the optional relation category type */
	function addFriendRequest($userId, $categoryId, $msg = '')
	{
		global $db, $session;

		if (!$session->id || !is_numeric($userId) || !is_numeric($categoryId) || haveContact(CONTACT_FRIEND, $session->id, $userId)) return false;

		$q = 'SELECT COUNT(reqId) FROM tblFriendRequests WHERE senderId='.$session->id.' AND recieverId='.$userId;
		if ($db->getOneItem($q)) return false;

		$q = 'INSERT INTO tblFriendRequests SET senderId='.$session->id.',recieverId='.$userId.',timeCreated=NOW(),categoryId='.$categoryId.',msg="'.$db->escape($msg).'"';
		$db->insert($q);
		return true;
	}

	/* Returns all pending requests sent from current user */
	function getSentFriendRequests()
	{
		global $db, $session;

		$q  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
		$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
		$q .= ' WHERE t1.senderId='.$session->id;
		$q .= ' ORDER BY t1.timeCreated DESC';

		return $db->getArray($q);
	}

	/* Returns all pending requests sent to $userId */
	function getRecievedFriendRequests()
	{
		global $db, $session;

		$q  = 'SELECT t1.*,t2.userName AS senderName,t3.categoryName FROM tblFriendRequests AS t1';
		$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.senderId=t2.userId)';
		$q .= ' LEFT JOIN tblCategories AS t3 ON (t1.categoryId=t3.categoryId)';
		$q .= ' WHERE t1.recieverId='.$session->id;
		$q .= ' ORDER BY t1.timeCreated DESC';

		return $db->getArray($q);
	}

	function getFriendRequest($requestId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($requestId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
		$q .= ' INNER JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
		$q .= ' WHERE t1.reqId='.$requestId;
		$q .= ' AND (t1.senderId='.$session->id.' OR t1.recieverId='.$session->id.')';

		return $db->getOneRow($q);
	}

	/* Deletes a friend request, only doable for the person who created the request */
	function removeSentFriendRequest($otherId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($otherId)) return false;

		$q  = 'DELETE FROM tblFriendRequests';
		$q .= ' WHERE recieverId='.$otherId.' AND senderId='.$session->id;
		$db->delete($q);
	}

	/* Deletes a friend request, only doable for the person who recieved the request */
	function denyFriendRequest($otherId)
	{
		global $db, $session, $config;
		if (!$session->id || !is_numeric($otherId)) return false;

		$q  = 'DELETE FROM tblFriendRequests';
		$q .= ' WHERE senderId='.$otherId.' AND recieverId='.$session->id;
		$db->delete($q);

		//tell the request sender that the request was denied
		$msg = nameLink($session->id).' denied your friend request.';
		systemMessage($otherId, 'Denied friend request', $msg);

		return true;
	}

	/* Deletes a friend request & creates a relation, only doable for the person who recieved the request */
	function acceptFriendRequest($otherId)
	{
		global $db, $session, $config;
		if (!$session->id || !is_numeric($otherId)) return false;

		$q  = 'DELETE FROM tblFriendRequests';
		$q .= ' WHERE senderId='.$otherId.' AND recieverId='.$session->id;
		$cnt = $db->delete($q);
		
		if ($cnt != 1) die('acceptfriendrequest xxkrash');

		//create a friend relation
		setContact(CONTACT_FRIEND, $session->id, $otherId);
		setContact(CONTACT_FRIEND, $otherId, $session->id);

		//tell the request sender that the request was accepted
		$msg = nameLink($session->id).' accepted your friend request, and has been added to your contact list.';
		systemMessage($otherId, 'Accepted friend request', $msg);
		return true;
	}

	/* Returns true if current user has a pending friend request with $userId */
	function hasPendingFriendRequest($userId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($userId)) return false;

		$q  = 'SELECT reqId FROM tblFriendRequests ';
		$q .= 'WHERE senderId='.$session->id.' AND recieverId='.$userId;

		if ($db->getOneItem($q)) return true;
		return false;
	}


?>
