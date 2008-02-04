<?
/**
 * $Id$
 *
 * Skeleton for user classes
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

class Users
{
	/**
	 * Looks up a username by id
	 */
	function getName($_id)
	{
		global $db, $session;

		if (!is_numeric($_id) || !$_id) return false;
		if ($_id == $session->id) return $session->username;

		$q = 'SELECT userName FROM tblUsers WHERE userId='.$_id;
		return $db->getOneItem($q);
	}

	/**
	 * Looks up usermode by id (normal, admin, super admin), returns a text string with the description
	 */
	function getMode($_id)
	{
		global $db, $session;

		if (!is_numeric($_id) || !$_id) return false;
		if ($_id == $session->id) {
			$mode = $session->mode;
		} else {
			$q = 'SELECT userMode FROM tblUsers WHERE userId='.$_id;
			$mode = $db->getOneItem($q);
		}
		
		return $session->userModes[$mode];
	}

	/**
	 * Set user mode to $_mode
	 */
	function setMode($_id, $_mode)
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

		$session->log('Changed usermode for '.getUserName($_id).' to '.$_mode);
		return true;
	}

	/**
	 * Returns the $_limit last users logged in, ordered by the latest logins first
	 */
	function lastLoggedIn($_limit = 50)
	{
		global $db, $session;
		if (!is_numeric($_limit)) return false;

		$q  = 'SELECT * FROM tblUsers ORDER BY timeLastLogin DESC';
		$q .= ' LIMIT 0,'.$_limit;
		return $db->getArray($q);
	}

	/**
	 * Returns array of all users online
	 */
	function allOnline()
	{
		global $db, $session;

		$q  = 'SELECT * FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastLogin DESC';
		return $db->getArray($q);
	}

	/**
	 * Returns number of users online
	 */
	function allOnlineCnt()
	{
		global $db, $session;

		$q  = 'SELECT COUNT(userId) FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of users
	 */
	function Cnt()
	{
		global $db;

		$q = 'SELECT COUNT(userId) FROM tblUsers';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of admins
	 */
	function AdminsCnt()
	{
		global $db;

		$q = 'SELECT COUNT(userId) FROM tblUsers WHERE userMode=1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of super admins
	 */
	function SuperAdminsCnt()
	{
		global $db;

		$q = 'SELECT COUNT(userId) FROM tblUsers WHERE userMode=2';
		return $db->getOneItem($q);
	}

	/**
	 * Admin function used by admin_list_users.php
	 */
	function getUsers($_mode = 0)
	{
		global $db;

		if (!is_numeric($_mode)) return false;

		$q = 'SELECT * FROM tblUsers';
		if ($_mode) $q .= ' WHERE userMode='.$_mode;
		return $db->getArray($q);
	}


	/**
	 * Admin function used by admin_todo_lists.php
	 */
	function getAdmins()
	{
		global $db;
		
		$q = 'SELECT * FROM tblUsers WHERE userMode=2';	//FIXME. ska denna bara returnera superadmins??
		return $db->getArray($q);
	}

	/**
	 * Returns a random user id
	 */
	function getRandomUserId()
	{
		global $db;

		$q = 'SELECT userId FROM tblUsers ORDER BY RAND() LIMIT 1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns userId of first match of username contains $phrase
	 */
	function searchUsernameContains($phrase)
	{
		global $db;

		$q  = 'SELECT userId FROM tblUsers ';
		$q .= 'WHERE LOWER(userName) LIKE LOWER("%'.$db->escape($phrase).'%") LIMIT 1';
		return $db->getOneItem($q);
	}


}
?>