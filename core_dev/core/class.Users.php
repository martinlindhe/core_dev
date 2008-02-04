<?
/**
 * $Id$
 *
 * Users class
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

		$session->log('Changed usermode for '.$this->getName($_id).' to '.$_mode);
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
	function cnt()
	{
		global $db;

		$q = 'SELECT COUNT(userId) FROM tblUsers';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of admins
	 */
	function adminCnt()
	{
		global $db;

		$q = 'SELECT COUNT(userId) FROM tblUsers WHERE userMode=1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of super admins
	 */
	function superAdminCnt()
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
	 * Returns userId of first match of username contains $phrase, for quick search
	 */
	function searchUsernameContains($phrase)
	{
		global $db;

		$q  = 'SELECT userId FROM tblUsers ';
		$q .= 'WHERE LOWER(userName) LIKE LOWER("%'.$db->escape($phrase).'%") LIMIT 1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns all usernames beginning with $phrase
	 */
	function searchUsernameBeginsWith($phrase)
	{
		global $db;

		$q  = 'SELECT userId,userName FROM tblUsers ';
		$q .= 'WHERE LOWER(userName) LIKE LOWER("'.$db->escape($phrase).'%")';
		return $db->getArray($q);
	}


	/**
	 * Randomly selects a user's presentation
	 */
	function randomUserPage()
	{
		$rnd = Users::getRandomUserId();
		header('Location: user.php?id='.$rnd);
		die;
	}

	/**
	 * Admin function used by admin_ip.php to show information about a IP-address
	 */
	function byIP($geoip)
	{
		global $db;

		if (!is_numeric($geoip)) return false;

		$q  = 'SELECT DISTINCT t1.userId,';
		$q .= '(SELECT userName FROM tblUsers WHERE userId=t1.userId) AS userName ';
 		$q .= 'FROM tblLogins AS t1 WHERE t1.IP='.$geoip;
		return $db->getArray($q);
	}

	/**
	 * Generates a link to user's page
	 */
	function link($id, $name = '')
	{
		if (!$id) return 'Guest';
		if (!$name) $name = Users::getName($id);
		if (!$name) die;

		return '<a href="'.getProjectPath(3).'user.php?id='.$id.'">'.$name.'</a>';
	}

	/**
	 * Generates a clickable thumbnail to user's page
	 */
	function linkThumb($id, $name = '')
	{
		global $config;

		if (!$id) return 'UNREGISTERED';
		if (!$name) $name = Users::getName($id);
		if (!$name) die;
		
		$pic_id = loadUserdataSetting($id, $config['settings']['default_image']);

		return makeThumbLink($pic_id, $name);
	}

	/**
	 * User's public presentation page
	 */
	function showUser($_userid_name = '')
	{
		global $db, $session, $config;

		if ($_userid_name && isset($_GET[$_userid_name]) && is_numeric($_GET[$_userid_name]) && $_GET[$_userid_name] != $session->id) {
			$userId = $_GET[$_userid_name];
			echo 'User overview:'.Users::getName($userId).'<br/>';

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

	/**
	 * Search users gadget
	 */
	function search()
	{
		global $session;

		echo '<h2>Search for users</h2>';

		if (isset($_POST['c'])) {
			$list = $this->getSearchResult($_POST);

			if (!empty($_POST['c'])) echo 'Search result for "'.$_POST['c'].'", ';
			else echo 'Custom search result, ';

			echo (count($list)!=1?count($list).' hits':'1 hit');
			echo '<br/><br/>';

			for ($i=0; $i<count($list); $i++) {
				echo Users::link($list[$i]['userId'], $list[$i]['userName']).'<br/>';
			}
			echo '<br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/>';

			echo '<br/>';

		} else if (isset($_GET['l']) && $_GET['l']) {
			/* Lista alla användare som börjar på en bokstav */

			$list = Users::searchUsernameBeginsWith($_GET['l']);

			echo 'Search result for users beginning with "'.$_GET['l'].'", ';

			echo (count($list)!=1?count($list).' hits':'1 hit');
			echo '<br/><br/>';

			for ($i=0; $i<count($list); $i++) {
				echo Users::link($list[$i]['userId'], $list[$i]['userName']).'<br>';
			}

			echo '<br>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/>';

		} else {

			echo 'Sort users beginning with: ';
			for ($i=ord('A'); $i<=ord('Z'); $i++) {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?l='.chr($i).'">'.chr($i).'</a> ';
			}
			echo '<br/><br/>';

			echo'<form name="src" method="post" action="'.$_SERVER['PHP_SELF'].'">';

			echo 'Free-text: ';
			echo '<input type="text" name="c" maxlength="20" size="20"/><br/>';

			$list = getUserdataFields();
			foreach ($list as $row) {
				if ($row['private'] || !$session->isAdmin) continue;
				echo getUserdataSearch($row).'<br/>';
			}

			echo '<input type="submit" class="button" value="Search"/>';
			echo '</form>';
		}
		echo '<script type="text/javascript">if (document.src) document.src.c.focus();</script>';
	}

	/**
	 * Used by Users::search()
	 *
	 * Data is $_POST and can contain irrelevant info!
	 */
	function getSearchResult($data)
	{
		global $db, $session;
		
		$data['c'] = trim($data['c']);
		$criteria = substr($data['c'], 0, 30); //only allow up to 30 characters in search free-text
		$criteria = $db->escape($data['c']);

		/* $criteria matchar vad som finns i alla textarea & textfält */
		$q  = 'SELECT t1.userId,t1.userName FROM tblUsers AS t1 ';
		if ($criteria) {
			$q .= 'LEFT JOIN tblSettings AS n1 ON (t1.userId=n1.ownerId AND n1.settingType='.SETTING_USERDATA.') ';
			$q .= 'LEFT JOIN tblUserdata AS t2 ON (n1.settingName=t2.fieldId) ';
		}

		$list = getUserdataFields();

		$start = 2; //autogenererade LEFT JOIN tables kommer heta n1, n2 osv.

		/* Add one INNER JOIN for each parameter we want to search on */
		foreach ($list as $row) {
			if (!empty($data['userdata_'.$row['fieldId'] ])) {
				$q .= 'LEFT JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId) ';
				$start++; //öka
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
		foreach ($list as $row) {
			if (!empty($data['userdata_'.$row['fieldId']])) {
				if (isset($x)) $q .= 'AND ';
				if ($start > 1) { // n1 första skapas alltid!
					$q .= '(n'.$start.'.settingName='.$row['fieldId'].' AND n'.$start.'.settingValue="'.$data['userdata_'.$row['fieldId']].'") ';
				}
				$start++;
				$x = 1;
			}
		}

		$q .= 'GROUP BY t1.userId ';
		$q .= 'ORDER BY t1.userName';

		return $db->getArray($q);
	}

}
?>