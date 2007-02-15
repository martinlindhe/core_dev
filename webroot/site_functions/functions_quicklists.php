<?
	//friends (quicklists) module settings:
	$config['friends']['denied_friend_request']		= ' har nekat din f&ouml;rfr&aring;gan att bli v&auml;nner.';
	$config['friends']['accepted_friend_request']	= ' har accepterat din f&ouml;rfr&aring;gan att bli v&auml;nner.';
	$config['nykompis_meddelande'] = ' har lagt till dig p&aring; sin kompislista!';

	/*
		functions_quicklists.php - Funktioner för kompislistor, ignore-listor och telefonböcker

	*/

	/* Quicklists types */
	define('QUICKLIST_FRIENDS',		1);
	define('QUICKLIST_IGNORES',		2);

	/* Predefined friendgroups */
	define('FRIENDGROUP_UNCATEGORIZED',	1);

	
	function addFriendGroup(&$db, $userId, $groupName)
	{
		if (!is_numeric($userId)) return false;
		$groupName = dbAddSlashes($db, $groupName);

		$sql = 'SELECT * FROM tblQuicklistGroups WHERE userId='.$userId.' AND groupName="'.$groupName.'"';
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return false;

		$query = dbQuery($db, 'INSERT INTO tblQuicklistGroups SET userId='.$userId.',groupName="'.$groupName.'"');
		return $db['insert_id'];
	}
	
	/* Raderar en kompisgrupp, om $delete_groupmembers = true så raderas alla medlemmar i gruppen med, annars flyttas dom till roten */
	function removeFriendGroup(&$db, $userId, $groupId, $delete_groupmembers = '')
	{
		if (!is_numeric($userId) || !is_numeric($groupId)) return false;

		$sql = 'DELETE FROM tblQuicklistGroups WHERE userId='.$userId.' AND groupId='.$groupId;
		dbQuery($db, $sql);
		if ($delete_groupmembers) {
			$sql = 'DELETE FROM tblQuicklists WHERE userId='.$userId.' AND groupId='.$groupId;
			dbQuery($db, $sql);
		} else {
			$sql = 'UPDATE tblQuicklists SET groupId=0 WHERE userId='.$userId.' AND groupId='.$groupId;
			dbQuery($db, $sql);
		}
	}

	function renameFriendgroup(&$db, $userId, $groupId, $newname)
	{
		if (!is_numeric($userId) || !is_numeric($groupId)) return false;
		$newname = dbAddSlashes($db, $newname);

		$sql = 'UPDATE tblQuicklistGroups SET groupName="'.$newname.'" WHERE userId='.$userId.' AND groupId='.$groupId;
		dbQuery($db, $sql);
	}

	function renamePhonebookEntry(&$db, $userId, $phoneId, $newname, $newnumber)
	{		
		if (!is_numeric($userId) || !is_numeric($phoneId)) return false;

		$newname   = dbAddSlashes($db, $newname);
		$newnumber = dbAddSlashes($db, fullPhonenumber($newnumber));
		if (!$newnumber) return false;

		$sql = 'UPDATE tblPhonebooks SET name="'.$newname.'",phonenumber="'.$newnumber.'" WHERE userId='.$userId.' AND phoneId='.$phoneId;
		dbQuery($db, $sql);
	}

	/* Returnerar alla kompis-kategorigrupper */
	function getFriendGroups(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT * FROM tblQuicklistGroups WHERE userId='.$userId.' ORDER by groupName ASC';
		return dbArray($db, $sql);
	}

	function getFriendGroupName(&$db, $userId, $groupId)
	{
		if (!is_numeric($userId) || !is_numeric($groupId)) return false;

		$sql = 'SELECT groupName FROM tblQuicklistGroups WHERE userId='.$userId.' AND groupId='.$groupId;
		return dbOneResultItem($db, $sql);
	}

	/* Returnerar namnet på gruppen $friendId är kategoriserad under */
	function getFriendGroup(&$db, $userId, $friendId)
	{
		if (!is_numeric($userId) || !is_numeric($friendId)) return false;

		//todo: gör om till en query med en inner join...
		$sql = 'SELECT * FROM tblQuicklists WHERE userId='.$userId.' AND data='.$friendId.' AND type='.QUICKLIST_FRIENDS;
		$row = dbOneResult($db, $sql);
		$name = getFriendGroupName($db, $userId, $row['groupId']);
		return $name;
	}
	
	/* Returns an array with $userId's all friends, including usernames & groupnames */
	function getUserFriends(&$db, $userId, $groupId = '')
	{
		if (!is_numeric($userId)) return false;
		
		$sql  = 'SELECT tblQuicklists.*, tblUsers.userName AS friendName, tblUsers.userId AS friendId, ';
		$sql .= 'tblSessions.timestamp AS lastActive, tblQuicklistGroups.groupName AS groupName, tblSessions.statusType AS statusType ';
		$sql .= 'FROM tblQuicklists ';
		$sql .= 'INNER JOIN tblUsers ON (tblUsers.userId = tblQuicklists.data) ';
		$sql .= 'LEFT OUTER JOIN tblSessions ON (tblSessions.userId = tblQuicklists.data) ';
		$sql .= 'LEFT OUTER JOIN tblQuicklistGroups ON (tblQuicklistGroups.groupId = tblQuicklists.groupId) ';
		$sql .= 'WHERE tblQuicklists.userId='.$userId.' ';
		$sql .= 'AND tblQuicklists.type='.QUICKLIST_FRIENDS.' ';
		if ($groupId && is_numeric($groupId)) {
			$sql .= 'AND tblQuicklists.groupId='.$groupId.' ';
		}

		$sql .= 'GROUP BY tblQuicklists.data ';
		$sql .= 'ORDER BY groupName ASC, tblUsers.userName ASC';

		return dbArray($db, $sql);
	}
	
	/* Returns an array with $userId's all friends, including usernames & excluding groupnames */
	function getUserFriendsFlat(&$db, $userId, $limit = 0)
	{
		if (!is_numeric($userId) || !is_numeric($limit)) return false;

		$sql  = 'SELECT t2.userName AS friendName,t2.userId AS friendId,t2.userStatus,t2.lastLoginTime,t2.lastActive ';
		$sql .= 'FROM tblQuicklists AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t2.userId = t1.data) ';
		$sql .= 'WHERE t1.userId='.$userId.' AND t1.type='.QUICKLIST_FRIENDS.' ';
		$sql .= 'GROUP BY t2.userName ';
		$sql .= 'ORDER BY t2.userName ASC';
		if ($limit) $sql .= ' LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}

	/* returns a array with all who have $userId as friend */
	function getOthersFriend(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT tblQuicklists.userId AS userId, tblQuicklistGroups.groupName AS groupName, tblUsers.userName AS userName ';
		$sql .= 'FROM tblQuicklists ';
		$sql .= 'INNER JOIN tblQuicklistGroups ON (tblQuicklists.groupId = tblQuicklistGroups.groupId) ';
		$sql .= 'INNER JOIN tblUsers ON (tblQuicklists.userId = tblUsers.userId) ';
		$sql .= 'WHERE tblQuicklists.data='.$userId;

		return dbArray($db, $sql);
	}

	/* Lägger till/uppdaterar en kompisrelation för $userId */
	function setFriend(&$db, $userId, $friendId, $groupId = 0)
	{
		if (!is_numeric($userId) || !is_numeric($friendId) || !is_numeric($groupId)) return false;

		$sql = 'SELECT * FROM tblQuicklists WHERE userId='.$userId.' AND type='.QUICKLIST_FRIENDS.' AND data='.$friendId;
		$check = dbQuery($db, $sql);

		//kan inte använda REPLACE eftersom tblQuicklists inte har nåt index
		if (!dbNumRows($check)) {
			$sql = 'INSERT INTO tblQuicklists SET userId='.$userId.',type='.QUICKLIST_FRIENDS.',data='.$friendId.',groupId='.$groupId;
			dbQuery($db, $sql);
		} else {
			$sql = 'UPDATE tblQuicklists SET groupId='.$groupId.' WHERE userId='.$userId.' AND type='.QUICKLIST_FRIENDS.' AND data='.$friendId;
			dbQuery($db, $sql);
		}
	}
	
	/* Returnerar true/false om du redan är kompis med friendId */
	function isFriend(&$db, $friendId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($friendId)) return false;

		$sql = 'SELECT userId FROM tblQuicklists WHERE userId='.$_SESSION['userId'].' AND type='.QUICKLIST_FRIENDS.' AND data='.$friendId;
		$check = dbQuery($db, $sql);

		if (dbNumRows($check)) return true;
		return false;
	}

	function removeFriend(&$db, $friendId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($friendId)) return false;

		$sql = 'DELETE FROM tblQuicklists WHERE userId='.$_SESSION['userId'].' AND data='.$friendId.' AND type='.QUICKLIST_FRIENDS;
		dbQuery($db, $sql);
	}

	/* Lägger till ett entry i användarens telefonbok */
	function addPhonebookEntry(&$db, $userId, $number, $name)
	{
		if (!is_numeric($userId)) return false;
		
		$name = dbAddSlashes($db, $name);
		$number = dbAddSlashes($db, fullPhonenumber($number));
		if (!$number) return false;

		$sql = 'SELECT userId FROM tblPhonebooks WHERE userId='.$userId.' AND (phonenumber="'.$number.'" OR name="'.$name.'")';
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return false;

		dbQuery($db, 'INSERT INTO tblPhonebooks SET userId='.$userId.',phonenumber="'.$number.'",name="'.$name.'"');
		return true;
	}
	
	function removePhonebookEntry(&$db, $userId, $phoneId)
	{
		if (!is_numeric($userId) || !is_numeric($phoneId)) return false;

		$sql = 'DELETE FROM tblPhonebooks WHERE userId='.$userId.' AND phoneId='.$phoneId;
		dbQuery($db, $sql);
		return true;
	}

	function getPhonebookList(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT * FROM tblPhonebooks WHERE userId='.$userId.' ORDER BY name ASC';
		return dbArray($db, $sql);
	}

	/* Returns the name associated with the ID */
	function getPhonenumberName(&$db, $userId, $phoneId)
	{
		if (!is_numeric($userId) || !is_numeric($phoneId)) return false;

		$sql = 'SELECT name FROM tblPhonebooks WHERE userId='.$userId.' AND phoneId='.$phoneId;
		return dbOneResultItem($db, $sql);
	}
	
	/* Returns the number associated with the ID */
	function getPhonenumber(&$db, $userId, $phoneId)
	{
		if (!is_numeric($userId) || !is_numeric($phoneId)) return false;

		$sql = 'SELECT phonenumber FROM tblPhonebooks WHERE userId='.$userId.' AND phoneId='.$phoneId;
		return dbOneResultItem($db, $sql);
	}

	/* Skickar in ett av användaren angivet telefonnummer och returnerar ett fullständigt telefonnummer, false om nummret är inkorrekt */
	/* Utgår från att nummret saknar landsprefix */
	function fullPhonenumber($number)
	{
		$data = strtolower(trim($number));
		$data = str_replace('-', '', $data);	//ta bort eventuella bindesträck		
		$data = str_replace('+', '', $data);	//ta bort eventuella plus (landsprefix)
		$data = str_replace('(', '', $data);
		$data = str_replace(')', '', $data);
		$data = str_replace(' ', '', $data);	//ta bort mellanslag
		
		if(substr($data,0,2) == '46') {
			$data = substr($data, 2);
		}

		//kontrollera att nummret bara innehåller siffror
		settype($data, 'double'); 
		settype($data, 'string'); //strängen kommer inte ha nån första nolla
		
		/* Vägra för korta/för långa nummer */
		if( (strlen($data)<7) || (strlen($data)>9) ) {
			return false;
		}
		return '+46'.$data;
	}

	/* Returns true/false if the $number is a swedish mobile phone number */
	/* $number must be +46 and all that, from fullPhonenumber() */
	function isSwedishMobile($number)
	{
		if (strlen($number) != 12) return false;

		switch (substr($number,0,6)) {
			case '+46702': return true;
			case '+46703': return true;
			case '+46704': return true;
			case '+46705': return true;
			case '+46706': return true;
			case '+46707': return true;
			case '+46708': return true;
			case '+46709': return true;
			
			case '+46730': return true;
			case '+46733': return true;
			
			case '+46736': return true;
			case '+46737': return true;
			case '+46739': return true;
		}
		return false;
	}

	/* Returns the top $limit users with lots of friends in their friendlists */
	function getUsersWithMostFriends(&$db, $limit = '')
	{
		$sql  = 'SELECT COUNT(tblQuicklists.data) AS cnt, tblQuicklists.userId, tblUsers.userName AS userName ';
		$sql .= 'FROM tblQuicklists ';
		$sql .= 'INNER JOIN tblUsers ON (tblQuicklists.userId = tblUsers.userId) ';
		$sql .= 'GROUP BY tblQuicklists.userId ';
		$sql .= 'ORDER BY cnt DESC';
		if (is_numeric($limit)) {
			$sql .= ' LIMIT 0,'.$limit;
		}

		return dbArray($db, $sql);
	}

	function displayFriendList(&$db, $userId)
	{
		global $config;
		
		$list = getUserFriendsFlat($db, $userId);

		if (count($list)) {
			$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0>';

			for ($i=0; $i<count($list); $i++) {
				if ($i % 2) {
					$bgcol = '#E0E0E0';
				} else {
					$bgcol = '#E4E0D7';
				}
				$str .= '<tr bgcolor="'.$bgcol.'"><td>';
				if (isset($list[$i]['timeLastActive']) && (time()-$list[$i]['timeLastActive'] < $config['session_timeout']) && ($list[$i]['timeLastActive'] > $list[$i]['timeLastLogout'])  ) {
					$str .= '<span class="breadbold">';
					$isonline = true;
				} else {
					$str .= '<span class="bread">';
					$isonline = false;
				}

				$str .= nameLink($list[$i]['friendId'], $list[$i]['friendName']);
				$str .= '</span>';
				if ($isonline == true) {
					$str .= ' ('.$list[$i]['userStatus'].')';
				}
				$str .= '</td><td width=14>';
				$str .= '<a href="mess_new.php?id='.$list[$i]['friendId'].'"><img border=0 align="absmiddle" width=14 height=10 border=0 src="gfx/brev_send.gif" alt="Skicka meddelande till '.$list[$i]['friendName'].'"></a>';
				$str .= '</td></tr>';
			}
			$str .= '</table>';
		} else {
			$str = 'Ingen venner p&aring; listen';
		}
			
		return $str;		
	}



	/* Adds a request-to-become-friends to $userId, from $_SESSION['userId'], with the optional $msg greeting */
	function addFriendRequest(&$db, $userId, $msg)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($userId) || isFriend($db, $userId)) return false;

		$msg = dbAddSlashes($db, $msg);
		
		$sql = 'INSERT INTO tblFriendRequests SET senderId='.$_SESSION['userId'].',recieverId='.$userId.',timeCreated='.time().',msg="'.$msg.'"';
		dbQuery($db, $sql);
		return true;
	}

	/* Returns all pending requests sent from $userId */
	function getSentFriendRequests(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
		$sql .= ' INNER JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
		$sql .= ' WHERE t1.senderId='.$userId;
		$sql .= ' ORDER BY t1.timeCreated DESC';
		
		return dbArray($db, $sql);
	}

	/* Returns all pending requests sent to $userId */
	function getRecievedFriendRequests(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS senderName FROM tblFriendRequests AS t1';
		$sql .= ' INNER JOIN tblUsers AS t2 ON (t1.senderId=t2.userId)';
		$sql .= ' WHERE t1.recieverId='.$userId;
		$sql .= ' ORDER BY t1.timeCreated DESC';
		
		return dbArray($db, $sql);
	}

	function getFriendRequest(&$db, $requestId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($requestId)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS recieverName FROM tblFriendRequests AS t1';
		$sql .= ' INNER JOIN tblUsers AS t2 ON (t1.recieverId=t2.userId)';
		$sql .= ' WHERE t1.reqId='.$requestId;
		$sql .= ' AND (t1.senderId='.$_SESSION['userId'].' OR t1.recieverId='.$_SESSION['userId'].')';

		return dbOneResult($db, $sql);
	}

	/* Deletes a friend request, only doable for the person who created the request */
	function removeSentFriendRequest(&$db, $requestId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($requestId)) return false;
		
		$sql  = 'DELETE FROM tblFriendRequests';
		$sql .= ' WHERE reqId='.$requestId.' AND senderId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}

	/* Deletes a friend request, only doable for the person who recieved the request */
	function denyFriendRequest(&$db, $requestId)
	{
		global $config;
		
		if (!$_SESSION['loggedIn'] || !is_numeric($requestId)) return false;
		
		$data = getFriendRequest($db, $requestId);
		if (!$data) return false;

		$sql  = 'DELETE FROM tblFriendRequests';
		$sql .= ' WHERE reqId='.$requestId.' AND recieverId='.$_SESSION['userId'];
		dbQuery($db, $sql);
		
		//tell the request sender that the request was denied
		addMessageToInbox($db, $config['messages']['system_id'], $data['senderId'], '', nameLink($_SESSION['userId'], $_SESSION['userName']).' '.$config['friends']['denied_friend_request'], MESSAGETYPE_INSTANT);

		return true;
	}

	/* Deletes a friend request & creates a relation, only doable for the person who recieved the request */
	function acceptFriendRequest(&$db, $requestId)
	{
		global $config;

		if (!$_SESSION['loggedIn'] || !is_numeric($requestId)) return false;
		
		$data = getFriendRequest($db, $requestId);
		if (!$data) return false;

		$sql  = 'DELETE FROM tblFriendRequests';
		$sql .= ' WHERE reqId='.$requestId.' AND recieverId='.$_SESSION['userId'];
		dbQuery($db, $sql);
		
		//create a friend relation
		setFriend($db, $_SESSION['userId'], $data['senderId']);
		setFriend($db, $data['senderId'], $_SESSION['userId']);
		
		//tell the request sender that the request was denied
		addMessageToInbox($db, $config['messages']['system_id'], $data['senderId'], '', nameLink($_SESSION['userId'], $_SESSION['userName']).' '.$config['friends']['accepted_friend_request'], MESSAGETYPE_INSTANT);

		return true;
	}

	/* Returns true if $_SESSION['userId'] has a pending friend request with $userId */
	function hasPendingFriendRequest(&$db, $userId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($userId)) return false;
		
		$sql  = 'SELECT reqId FROM tblFriendRequests ';
		$sql .= 'WHERE senderId='.$_SESSION['userId'].' AND recieverId='.$userId;
		
		$check = dbOneResultItem($db, $sql);
		if ($check) return true;
		
		return false;
	}

?>