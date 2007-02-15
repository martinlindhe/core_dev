<?
	$config['login_encrypted'] = 0; //dont send the clear text login password over http, but sends sha1 encoded string from client. requires javascript
	$config['login_sha1_key'] = 'sitecode_uReply';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['user_max_allowed']	= 20000;	//max number of user accounts allowed

	//user module settings:
	$config['user']['min_password_length'] = 6;



	/* Skeleton function used for remote user log-in */
	function newUserEntry(&$db, $userName, $userMode = 0)
	{
		global $config;

		if (!is_numeric($userMode)) return false;

		$userName = dbAddSlashes($db, $userName);

		$sql = 'INSERT INTO tblUsers SET userName="'.$userName.'",userPass="",userMode='.$userMode.',timeCreated=NOW()';

		dbQuery($db, $sql);
		$newUserId = $db['insert_id'];

		/* Creates a Inbox and Outbox */
		if ($config['messages']['enabled']) {
			addUserMessageFolder($db, $newUserId, $config['messages']['folder_inbox'],  MESSAGE_FOLDER_STATIC);
			addUserMessageFolder($db, $newUserId, $config['messages']['folder_outbox'], MESSAGE_FOLDER_STATIC);
		}

		return $newUserId;
	}


	//$userMode=0 for normal users
	//$userMode=1 for administrators
	//$userMode=2 for super adiministrators
	function makeNewUser(&$db, $username, $password1, $password2, $userMode = 0)
	{
		global $config;

		if (!is_numeric($userMode)) return false;

		$username = trim($username);
		$password1 = trim($password1);
		$password2 = trim($password2);
		
		if ((dbAddSlashes($db, $username) != $username) || (dbAddSlashes($db, $password1) != $password1)) {
			//if someone tries to enter ' or " etc as username/password letters
			//with this check, we dont need to encode the strings for use in sql query
			return 'Username or password contains invalid characters';
		}
		
		if ($password1 && $password2 && ($password1 != $password2)) {
			return 'The passwords doesnt match';
		}
		
		if (strlen($username) < 3) return 'Username must be at least 3 characters long';
		if (strlen($password1) < 4) return 'Password must be at least 4 characters long';

		$sql = 'SELECT userId FROM tblUsers WHERE userName="'.$username.'"';
		$checkId = dbOneResultItem($db, $sql);
		echo $checkId;
		if ($checkId) {
			return 'Username already exists';
		}

		$sql = 'INSERT INTO tblUsers SET userName="'.$username.'",userPass="'.sha1( sha1($config['login_sha1_key']).sha1($password1) ).'",userMode='.$userMode.',timeCreated=NOW()';
		dbQuery($db, $sql);
		$newUserId = $db['insert_id'];

		logEntry($db, 'User <b>'.$username.'</b> created');

		/* Creates a Inbox and Outbox */
		if ($config['messages']['enabled']) {
			addUserMessageFolder($db, $newUserId, $config['messages']['folder_inbox'],  MESSAGE_FOLDER_STATIC);
			addUserMessageFolder($db, $newUserId, $config['messages']['folder_outbox'], MESSAGE_FOLDER_STATIC);
		}

		return $newUserId;
	}
	
	//if remote_login = true, then $username contains the userId to log in, and password is blank
	function loginUser(&$db, $username, $password)
	{
		global $config;

		//fixme: return false if username contain other than a-z A-Z 0-9 ._-
		$username = dbAddSlashes($db, trim(strip_tags($username)));
		$sha1_pwd = sha1( sha1($config['login_sha1_key']).sha1($password) );
	
		if (!$username || !$password) return false;

		//call stored procedure
		$sql = 'CALL getUser("'.$username.'", "'.$sha1_pwd.'")';
		$data = dbOneResult($db, $sql);

		if (!$data) {
			logEntry($db, 'Wrong login info (user '.htmlentities($username).', password '.htmlentities($password).')');
			return false;
		}

		return StartSession($db, $username, $data);
	}
	
	function getUserId(&$db, $username)
	{
		/* Looks up userId for $username, returns false if $username dont exist */

		$username = dbAddSlashes($db, $username);

		$sql = 'SELECT userId FROM tblUsers WHERE userName="'.$username.'"';
		return dbOneResultItem($db, $sql);
	}

	function getUserName(&$db, $userId)
	{
		if (!is_numeric($userId) || !$userId) return false;

		return dbOneResultItem($db, 'SELECT userName FROM tblUsers WHERE userId='.$userId);
	}

	function getUserMode(&$db, $userId)
	{
		if (!is_numeric($userId) || !$userId) return false;

		return dbOneResultItem($db, 'SELECT userMode FROM tblUsers WHERE userId='.$userId);
	}
	
	function getUserData(&$db, $userId)
	{
		if (!is_numeric($userId) || !$userId) return false;

		return dbOneResult($db, 'SELECT * FROM tblUsers WHERE userId='.$userId);
	}

	/* Returns the number of registered users in the database */
	function getUserCount(&$db)
	{
		return dbOneResultItem($db, 'SELECT COUNT(userId) FROM tblUsers');
	}

	function getAdministrators(&$db)
	{
		$sql = 'SELECT * FROM tblUsers WHERE userMode>0 ORDER BY userName ASC';
		return dbArray($db, $sql);
	}

	function setUserStatus(&$db, $text)
	{
		if (!$_SESSION['loggedIn']) return false;
		$text = dbAddSlashes($db, $text);

		$sql = 'UPDATE tblUsers SET userStatus="'.$text.'" WHERE userId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}

	function setUserName(&$db, $userId, $userName)
	{
		if (!is_numeric($userId)) return false;
		$userName = dbAddSlashes($db, trim($userName));
		if (!$userName) return false;

		$sql = 'UPDATE tblUsers SET userName="'.$userName.'" WHERE userId='.$userId;
		dbQuery($db, $sql);
	}

	function setUserPassword(&$db, $userId, $password)
	{
		global $config;

		if (!is_numeric($userId)) return false;

		$password = dbAddSlashes($db, $password);

		dbQuery($db, "UPDATE tblUsers SET userPass='".sha1( sha1($config['login_sha1_key']).sha1($password))."' WHERE userId=".$userId);
		return true;
	}

	/* Returnerar en textsträng beskrivande vad användaren pysslar med för tillfället */
	function getUserStatus(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		if (userIsLoggedIn($db, $userId)) {
			//return dbOneResultItem($db, 'SELECT userStatus FROM tblUsers WHERE userId='.$userId);
			return 'Online';
		}

		//return 'Offline (senast inne '.strtolower(getRelativeTimeLong(getUserLastLogin($db, $userId))).')';
		return 'Offline';
	}

	function getUserCreated(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT timeCreated FROM tblUsers WHERE userId='.$userId;
		return dbOneResultItem($db, $sql);
	}

	/* Returns TRUE if user is still logged in, or false if session timed out or logged out manually */
	function userIsLoggedIn(&$db, $userId)
	{
		global $config;
		
		if (!is_numeric($userId)) return false;
		
		$sql = 'SELECT lastActive FROM tblUsers WHERE userId='.$userId;
		$lastActive = dbOneResultItem($db, $sql);

		$timeDiff = time()-$lastActive;

		if ($timeDiff > $config['session_timeout']) return false;
		return true;
	}
	
	function updateUserActivity(&$db)
	{
		$sql = 'UPDATE tblUsers SET lastActive=NOW() WHERE userId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}

	function getUserLastLogin(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT lastLoginTime FROM tblUsers WHERE userId='.$userId;
		return dbOneResultItem($db, $sql);
	}

	function logVisitor(&$db, $userId)
	{
		if (!is_numeric($userId) || !$_SESSION['loggedIn']) return false;

		$sql = 'INSERT INTO tblVisitors SET userId='.$userId.',visitorId='.$_SESSION['userId'].',timestamp='.time();
		dbQuery($db, $sql);
	}

	function getLastVisitors(&$db, $userId, $count = '')
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT tblVisitors.visitorId,tblVisitors.timestamp,tblUsers.userName AS visitorName ';
		$sql .= 'FROM tblVisitors ';
		$sql .= 'INNER JOIN tblUsers ON (tblVisitors.visitorId=tblUsers.userId) ';
		$sql .= 'WHERE tblVisitors.userId='.$userId.' ';
		$sql .= 'ORDER BY tblVisitors.timestamp DESC';
		if (is_numeric($count)) {
			$sql .= ' LIMIT 0,'.$count;
		}

		return dbArray($db, $sql);
	}

	/* Raderar en användare och all användarinfo den är associerad med */
	function removeUser(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		dbQuery($db, 'DELETE FROM tblUsers WHERE userId='.$userId);

/*
		dbQuery($db, 'DELETE FROM tblAccessgroupMembers WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblActivation WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblDiaries WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblFileGroups WHERE ownerId='.$userId);
		dbQuery($db, 'DELETE FROM tblForums WHERE authorId='.$userId);
		dbQuery($db, 'DELETE FROM tblGuestbooks WHERE userId='.$userId.' OR authorId='.$userId);
		dbQuery($db, 'DELETE FROM tblLoginStats WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblMatchmakingAnswers WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblMessageFolders WHERE ownerId='.$userId);
		dbQuery($db, 'DELETE FROM tblMessages WHERE messageOwner='.$userId);
		dbQuery($db, 'DELETE FROM tblPhonebooks WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblPollVotes WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblQuicklistGroups WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblQuicklists WHERE userId='.$userId.' OR data='.$userId);
		dbQuery($db, 'DELETE FROM tblStatistics WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblSubscriptions WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblUserdata WHERE userId='.$userId);
		dbQuery($db, 'DELETE FROM tblVisitors WHERE userId='.$userId);
*/
		removeFromModerationQueueByItemId($db, $userId, MODERATION_REPORTED_USER);
	}

	function displayLastVisitors(&$db, $userId, $limit = 5)
	{
		$list = getLastVisitors($db, $userId, $limit);
		$str = '';

		if (count($list)) {
			$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr><td width=4>&nbsp;</td><td>';

			for ($i=0; $i<count($list); $i++) {
				$str .= getRelativeTimeLong($list[$i]['timestamp']).': ';
				$str .= nameLink($list[$i]['visitorId'], $list[$i]['visitorName']).'<br>';
			}

			$str .= '</td></tr></table>';
		} else {
			$str .= 'Inga bes&ouml;kare loggade';
		}
		
		return $str;
	}

	function displayUserLatestLogins(&$db, $userId)
	{
		if (isset($_GET["p"])) {
			$page = $_GET["p"];
		} else {
			$page = 1;
		}

		$list = getUserLastLogins($db, $userId, $page);

		if (count($list)) {
			$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr><td width=4>&nbsp;</td><td>';

			for($i=0; $i<count($list); $i++) {
				$str .= getRelativeTimeLong($list[$i]['timestamp']).': ';
				$str .= $list[$i]['userIp'].'<br>';
			}

			$logincnt = getUserLastLoginsCount($db, $userId);
			$str .= pageCounter($logincnt, LOGIN_ENTRIES_PER_PAGE, $_SERVER['PHP_SELF'].'?id='.$userId, $page, 8);
			$str .= '<br>';
			$str .= $logincnt.' inloggningar totalt<br>';
			$str .= '</td></tr></table><br>';
		} else {
			$str = 'Inga inloggningar loggade';
		}
		
		return $str;
	}

	/* om $page = '' så första sidan */
	function getUserLastLogins(&$db, $userId, $page = 1)
	{
		if (!is_numeric($userId) || !is_numeric($page)) return false;

		$sql  = 'SELECT * FROM tblLoginStats WHERE userId='.$userId.' ORDER BY timestamp DESC';
		$sql .= ' LIMIT ' . ((LOGIN_ENTRIES_PER_PAGE) * ($page-1)). ','. (LOGIN_ENTRIES_PER_PAGE);
		return dbArray($db, $sql);
	}

	function getUserLastLoginsCount(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT COUNT(userId) AS cnt FROM tblLoginStats WHERE userId='.$userId;
		return dbOneResultItem($db, $sql);
	}

	function displayUserAccessgroups(&$db, $userId)
	{
		if (!$_SESSION['isAdmin']) return;

		/* Add user to accessgroup */
		if (isset($_POST['accessgroup'])) {
			addAccessgroupMember($db, $_POST['accessgroup'], $userId);
		}

		/* Remove user from accessgroup */
		if (isset($_GET['removefromaccessgroup'])) {
			removeAccessgroupMember($db, $_GET['removefromaccessgroup'], $userId);
		}

		$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr><td width=4>&nbsp;</td><td>';

		$curr_list = getUserAccessgroups($db, $userId);
		if (count($curr_list)>0) {
			$str .= 'Medlem i dessa accessgrupper:<br>';
			$str .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
			for ($i=0; $i<count($curr_list); $i++) {
				$str .= '<tr>';
				$str .= '<td><b>'.$curr_list[$i]['groupName'].'</b></td>';
				$str .= '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&removefromaccessgroup='.$curr_list[$i]['groupId'].'">Ta bort</a></td>';
				$str .= '</tr>';
			}
			$str .= '</table>';
			$str .= '<br>';
		}

		/* Lägg till/ta bort från accessgrupper */
		$list = getAccessgroups($db);
		if (count($list)) {
			$str .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
			$str .= '<form name="addAccessgroup" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$userId.'">';
			$str .= '<tr><td>L&auml;gg till i ';
			$str .= '<select name="accessgroup">';
			for ($i=0; $i<count($list); $i++) {
				$found=0;
				for ($j=0; $j<count($curr_list); $j++) {
					if ($curr_list[$j]['groupId'] == $list[$i]['groupId']) $found=1;
				}
				if ($found==0) {
					$str .= '<option value="'.$list[$i]['groupId'].'">'.$list[$i]['groupName'];
				}
			}
			$str .= '</select></td></tr>';
			$str .= '<tr><td><input type="submit" class="button" value="L&auml;gg till"></td></tr>';
			$str .= '</form>';
			$str .= '</table>';
		}
		$str .= '</td></tr></table>';
		
		return $str;
	}

	/* queryn returnerar:
	
		timeLastLogin - senaste inloggningen
		timeLastActive - senast aktiv
		timeLastLogout - senaste registrerade utloggningen
	*/	
	function getUsersOnline(&$db, $limit = 0)
	{
		if (!is_numeric($limit)) return false;

		global $config;

		$sql  = 'SELECT t1.userId,t1.userName,t1.userStatus,t1.lastLoginTime,t1.lastActive ';
		$sql .= 'FROM tblUsers AS t1 ';
		//$sql .= 'LEFT OUTER JOIN tblLoginStats AS t2 ON (t1.userId=t2.userId AND t2.loggedOut=1) ';
		
		$sql .= 'WHERE ('.time().'-t1.lastActive) < '.$config['session_timeout'].' ';
		
		//$sql .= 'GROUP BY t1.userId HAVING t1.lastActive>MAX(t2.timestamp) ';
		$sql .= 'ORDER BY t1.lastActive DESC';

		if ($limit > 0) {
			$sql .= ' LIMIT 0,'.$limit;
		}

		return dbArray($db, $sql);
	}

	function getUsersOnlineCount(&$db)
	{
		//todo: gör en egen query
		
		$list = getUsersOnline($db);
		return count($list);
	}
	
	function displayLatestLogins(&$db, $limit = 4)
	{
		global $config;
		
		$list = getLastLogins($db, $limit);

		$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr><td width=4>&nbsp;</td>';

			for ($i=0; $i<count($list); $i++) {
				$str .= '<td valign="top">';

				$str .=  '<a href="user_show.php?id='.$list[$i]['userId'].'">';
				$userpic = getThumbnail($db, $list[$i]['userId'], USERFIELD_PICTURE, $config['thumbnail_width'], $config['thumbnail_height'], false);
				if ($userpic) {
					$str .= $userpic.'<br>';
				} else {
					$str .= '<img src="gfx/nopict_text.gif" border=0 width='.$config['thumbnail_width'].'><br>';
				}

				$str .= '<b>'.$list[$i]['userName'].'</a></b><br>';
				$str .= wordwrap(getRelativeTimeLong($list[$i]['timestamp']),17,'<br>');
				$str .= '</td>';
			}

		$str .= '</tr></table>';
		
		return $str;
	}

	/* Returns the $limit last logins, unique users only */
	function getLastLogins(&$db, $limit)
	{
		if (!is_numeric($limit)) return false;

		$sql  = 'SELECT t1.userId, MAX(t1.timestamp) AS timestamp, t2.userName ';
		$sql .= 'FROM tblLoginStats AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId = t2.userId) ';
		$sql .= 'GROUP BY t1.userId ';
		$sql .= 'ORDER BY timestamp DESC ';
		$sql .= 'LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}

	/* Returns TRUE if password is correct, used for change password checking */
	function checkPassword(&$db, $userId, $password)
	{
		global $config;

		if (!is_numeric($userId)) return false;
		$password = addslashes($password);
		
		$check = dbQuery($db, "SELECT userId FROM tblUsers WHERE userId=".$userId." AND userPass='".sha1( sha1($config['login_sha1_key']).sha1($password) )."'");
		if (dbNumRows($check)) return true;
		return false;
	}

?>