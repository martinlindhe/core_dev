<?
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

		$q = 'SELECT * FROM tblUsers WHERE timeLastLogin >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastLogin DESC';

		return $db->getArray($q);
	}
?>