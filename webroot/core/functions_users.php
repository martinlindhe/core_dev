<?
	$config['user']['log_visitors'] = true;	//log each visit on users personal page from another user

	/* Looks up a username by id */
	function getUserName($_id)
	{
		global $db;

		if (!is_numeric($_id) || !$_id) return false;

		$q = 'SELECT userName FROM tblUsers WHERE userId='.$_id;
		return $db->getOneItem($q);
	}

	//returns the $_limit last users logged in, ordered by the latest logins first
	function getUsersLastLoggedIn($_limit = 50)
	{
		global $db, $session;
		if (!is_numeric($_limit)) return false;

		$q = 'SELECT * FROM tblUsers ORDER BY timeLastLogin DESC';
		$q .= ' LIMIT 0,'.$_limit;

		return $db->getArray($q);
	}

	//
	function getUsersOnline()
	{
		global $db, $session;

		$q = 'SELECT * FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastLogin DESC';

		return $db->getArray($q);
	}

	function getUserVisitors($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q = 'SELECT t1.*,t2.userName AS creatorName FROM tblVisits AS t1 ';
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
	
	
?>