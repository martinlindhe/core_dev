<?
	//functions_contacts.php - implements friend lists. also implements blocked contacts
	
	define('CONTACT_FRIEND', 1);
	define('CONTACT_BLOCKED', 2);

	function haveContact($_type, $otherId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($otherId)) return false;

		$q = 'SELECT contactId FROM tblContacts WHERE userId='.$session->id.' AND otherUserId='.$otherId.' AND contactType='.$_type;
		return $db->getOneItem($q);
	}

	function removeContact($_type, $otherId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($otherId)) return false;

		$q = 'DELETE FROM tblContacts WHERE userId='.$session->id.' AND otherUserId='.$otherId.' AND contactType='.$_type;
		$db->query($q);
	}

	/* Adds or updates a user contact (relation with another user) */
	function setContact($_type, $otherId, $groupId = 0)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($otherId) || !is_numeric($groupId)) return false;

		if (!haveContact($_type, $otherId)) {
			/* Create new contact */
			$q = 'INSERT INTO tblContacts SET userId='.$session->id.',contactType='.$_type.',otherUserId='.$otherId.',groupId='.$groupId.',timeCreated=NOW()';
			$db->query($q);
		} else {
			/* Change the contact group */
			$q = 'UPDATE tblContacts SET groupId='.$groupId.' WHERE userId='.$session->id.' AND contactType='.$_type.' AND otherUserId='.$otherId;
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
	
	/* Returns an array with $userId's all friends, including usernames but no other info */
	function getContactsFlat($_type, $userId)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($userId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS contactName ';
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
			setContact(CONTACT_FRIEND, $userId);
		}

		if ($session->id != $userId && isset($_GET['removefriend'])) {
			removeContact(CONTACT_FRIEND, $userId);
		}

		$list = getContactsFlat(CONTACT_FRIEND, $userId);

		if ($session->id != $userId) {
			echo 'User '.$userId.'s friend list:<br/>';
			if (!haveContact(CONTACT_FRIEND, $userId)) {
				echo '<a href="?id='.$userId.'&addfriend">Become friends</a><br/>';
			} else {
				echo '<a href="?id='.$userId.'&removefriend">Remove friend contact</a><br/>';
			}
		} else {
			echo 'Your friend list:<br/>';
		}

		if (!count($list)) {
			echo 'No friends.';
			return;
		}

		echo '<table width="100%" cellpadding=0 cellspacing=0 border=1>';

		foreach ($list as $row) {
			echo '<tr bgcolor="#E4E0D7"><td>';
			//fixme: show online koden e paj
			if (isset($row['timeLastActive']) && (time()-$row['timeLastActive'] < $config['session_timeout']) && ($row['timeLastActive'] > $row['timeLastLogout'])  ) {
				echo '<span class="breadbold">';
				$isonline = true;
			} else {
				echo '<span class="bread">';
				$isonline = false;
			}

			echo nameLink($row['contactId'], $row['contactName']);
			echo '</span>';
			if ($isonline == true) {
				echo ' ('.$row['userStatus'].')';
			}
			echo '</td><td width=20>';
			echo '<a href="mess_new.php?id='.$row['contactId'].'"><img align="absmiddle" width=14 height=10 border=0 src="/gfx/icon_mail.png" alt="Send a message to '.$row['contactName'].'"></a>';
			echo '</td></tr>';
		}
		echo '</table>';
	}

?>