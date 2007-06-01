<?
	require_once('atom_settings.php');

	$config['user']['log_visitors'] = true;	//log each visit on users personal page from another user

	/* Looks up a username by id */
	function getUserName($_id)
	{
		global $db, $session;

		if (!is_numeric($_id) || !$_id) return false;
		if ($_id == $session->id) return $session->username;

		$q = 'SELECT userName FROM tblUsers WHERE userId='.$_id;
		return $db->getOneItem($q);
	}

	//returns the $_limit last users logged in, ordered by the latest logins first
	function getUsersLastLoggedIn($_limit = 50)
	{
		global $db, $session;
		if (!is_numeric($_limit)) return false;

		$q  = 'SELECT * FROM tblUsers ORDER BY timeLastLogin DESC';
		$q .= ' LIMIT 0,'.$_limit;
		return $db->getArray($q);
	}

	//
	function getUsersOnline()
	{
		global $db, $session;

		$q  = 'SELECT * FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastLogin DESC';
		return $db->getArray($q);
	}

	function getUsersOnlineCnt()
	{
		global $db, $session;

		$q  = 'SELECT COUNT(userId) FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastLogin DESC';
		return $db->getOneItem($q);
	}

	function getUsersCnt()
	{
		global $db, $session;

		$q = 'SELECT COUNT(userId) FROM tblUsers';
		return $db->getOneItem($q);
	}

	function getAdminsCnt()
	{
		global $db, $session;

		$q = 'SELECT COUNT(userId) FROM tblUsers WHERE userMode=1';
		return $db->getOneItem($q);
	}

	function getSuperAdminsCnt()
	{
		global $db, $session;

		$q = 'SELECT COUNT(userId) FROM tblUsers WHERE userMode=2';
		return $db->getOneItem($q);
	}

	function getUserVisitors($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblVisits AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE ownerId='.$_id.' ORDER BY timeCreated DESC';
		return $db->getArray($q);
	}

	function showUser($_userid_name = '')
	{
		global $db, $session, $config;

		if ($_userid_name && isset($_GET[$_userid_name]) && is_numeric($_GET[$_userid_name]) && $_GET[$_userid_name] != $session->id) {
			$userId = $_GET[$_userid_name];
			echo 'User overview:'.getUserName($userId).'<br/>';

			if ($config['user']['log_visitors']) {
				$q = 'INSERT INTO tblVisits SET ownerId='.$userId.',creatorId='.$session->id.',timeCreated=NOW()';
				$db->insert($q);
			}

		} else {
			$userId = $session->id;
			echo 'Your overview:<br/>';
		}

		echo 'show public settings - todo';
	}

	/* Admin function used by admin_ip.php to show information about a IP-address */
	function getUsersByIP($geoip)
	{
		global $db;

		if (!is_numeric($geoip)) return false;

		$q  = 'SELECT DISTINCT t1.userId,';
		$q .= '(SELECT userName FROM tblUsers WHERE userId=t1.userId) AS userName ';
 		$q .= 'FROM tblLogins AS t1 WHERE t1.IP='.$geoip;
		return $db->getArray($q);
	}

	/* Admin function used by admin_list_users.php */
	function getUsers($_mode = 0)
	{
		global $db;

		if (!is_numeric($_mode)) return false;

		$q = 'SELECT * FROM tblUsers';
		if ($_mode) $q .= ' WHERE userMode='.$_mode;
		return $db->getArray($q);
	}


	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXX NOT CLEAN CODE BELOW
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

	/* data är $_POST å kan innehålla irrelevant info! */
	function getUserSearchResult($data)
	{
		global $db;

		$data['c'] = trim($data['c']);
		$criteria = substr($data['c'], 0, 30); //only allow up to 30 characters in search free-text
		$criteria = $db->escape($data['c']);

		/* $criteria matchar vad som finns i alla textarea & textfält */
		$q  = 'SELECT t1.userId,t1.userName FROM tblUsers AS t1 ';
		$q .= 'LEFT JOIN tblSettings AS n1 ON (t1.userId=n1.ownerId AND n1.settingType='.SETTING_USERDATA.') ';
		$q .= 'LEFT JOIN tblUserdata AS t2 ON (n1.settingName=t2.fieldId) ';

		$list = getUserdataFields();

		/* Kollar ifall nån data är satt i $data[] ... */
		$found = false;
		foreach ($list as $row) {
			if (!empty($data[$row['fieldId']])) {
				$found = true;
				break;
			}
		}

		/* ... eller sätter $x om vi bara ska returnera alla användarna */
		/* om man är admin, så returneras alla användare om $data är tom */
		if ( !$criteria && $_SESSION['isAdmin'] && !$found) {
			$x = 1;
		} else {

			$start = 2; //autogenererade INNER JOIN tables kommer heta a1, a2 osv.

			/* Add one INNER JOIN for each parameter we want to search on */
			for ($i=0; $i<count($list); $i++) {
				if (isset($data[ $list[$i]['fieldId'] ])) {

					if ($data[ $list[$i]['fieldId'] ]) {
						$q .= 'INNER JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId) ';
						$start++; //öka
					}
				}
			}

			$q .= 'WHERE ';
			if ($criteria) { //för fritext
				$q .= '((n1.settingType='.USERDATA_TYPE_TEXT.' OR n1.settingType='.USERDATA_TYPE_TEXTAREA.') ';
				$q .= 'AND LOWER(n1.settingValue) LIKE LOWER("%'.$criteria.'%") AND t2.private!=1) ';
				$q .= 'OR LOWER(t1.userName) LIKE LOWER("%'.$criteria.'%") ';
				$x = 1;
			}

			$start = 2; //autogenererade INNER JOIN tables kommer heta a1, a2 osv.

			/* Plocka fram dom userfält användaren har sökt på */
			for ($i=0; $i<count($list); $i++) {
				if (isset($data[ $list[$i]['fieldId'] ])) {
					$val = $data[ $list[$i]['fieldId'] ];
					if ($val) {
						if (isset($x)) {
							$q .= 'AND ';
						}
						if ($start > 1) { // n1 första skapas alltid!
							$q .= '(n'.$start.'.settingName='.$list[$i]['fieldId'].' AND n'.$start.'.settingValue="'.$val.'") ';
						}
						$start++;
						$x = 1;
					}
				}
			}

		}

		$q .= 'GROUP BY t1.userId ';
		$q .= 'ORDER BY t1.userName';

		if (!isset($x)) return;

		return $db->getArray($q);
	}

	/* Returnerar alla användarnamn som börjar på $phrase */
	function searchUsernameBeginsWith($phrase)
	{
		global $db;

		$q  = 'SELECT userId,userName FROM tblUsers ';
		$q .= 'WHERE LOWER(userName) LIKE LOWER("'.$db->escape($phrase).'%")';

		return $db->getArray($q);
	}

	function setUserMode($_id, $_mode)
	{
		global $db, $session;
		if (!$session->isSuperAdmin || !is_numeric($_id) || !is_numeric($_mode)) return false;
		
		$q = 'UPDATE tblUsers SET userMode='.$_mode.' WHERE userId='.$_id;
		$db->update($q);

		if ($_id == $session->id) return true;

		switch ($_mode) {
			case 0: $msg = $session->username.' has reduced your usermode to normal member.'; break;
			case 1: $msg = $session->username.' has granted you admin rights.'; break;
			case 2: $msg = $session->username.' has granted you super admin rights.'; break;
		}

		sendMessage($_id, 'System message', $msg);
		return true;
	}


	/*
	//todo: gör till "sök i ett userfält" funktion, ta tillexempel 'Nickname' eller 'E-mail' som parameter
	function getUserSearchResultOnNickname($search_phrase)
	{
		global $db;

		$search_phrase = $db->escape(trim($search_phrase));
		$fieldId = getUserdataFieldIdByName('Nickname');

		$q  = 'SELECT t1.userId,t1.userName FROM tblUsers AS t1 ';
		$q .= 'INNER JOIN tblSettings AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'WHERE t2.fieldId='.$fieldId.' AND LOWER(t2.value) LIKE LOWER("%'.$search_phrase.'%")';

		return $db->getArray($q);
	}	*/
?>