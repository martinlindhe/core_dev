<?php
/**
 * $Id$
 *
 * Users class
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('atom_visits.php');		//for logVisit()
require_once('functions_messages.php');	//for sendMessage()

$config['user']['log_visitors'] = true;	//XXX move setting somewhere else!

//TODO: separate into one  User() and one Users() class and make it actually OO -will break much code so do this LATER!

class Users
{
	function reserveId()
	{
		global $db;

		$q = 'INSERT INTO tblUsers SET userMode=0';
		return $db->insert($q);
	}

	function registerUser($username, $mode)
	{
		global $db;
		if (!is_numeric($mode)) return false;

		$q = 'INSERT INTO tblUsers SET userName="'.$db->escape($username).'",userMode='.$mode.',timeCreated=NOW()';
		return $db->insert($q);
	}

	function updateUser($id, $username, $mode)
	{
		global $db;
		if (!is_numeric($id) || !is_numeric($mode)) return false;

		$q = 'UPDATE tblUsers SET userName="'.$db->escape($username).'",userMode='.$_mode.',timeCreated=NOW() WHERE userId='.$id;
		$db->update($q);
	}

	/**
	 * Sets a new password for the user
	 *
	 * @param $_id user id
	 * @param $_pwd1 password to set
	 * @param $_pwd2 password to compare with (optional)
	 */
	function setPassword($_id, $_pwd1, $_pwd2 = '', $key = '')
	{
		global $db, $h;
		if (!is_numeric($_id)) return false;
		/* This function is referenced in the Auth-class too, but in that
		 * context you cant use the $auth-object, since it's itself.
		 * If $key is empty, the reference wasn't from the Auth-class
		 * so therefore use the key from the Auth-object instead.
		 */
		if (empty($key)) $key = $h->auth->sha1_key;

		if ($_pwd2) {
			if (strlen($_pwd1) < 4) {
				$h->error = t('Password must be at least 4 characters long');
				return false;
			}

			if ($_pwd1 != $_pwd2) {
				$h->error = t('The passwords doesnt match');
				return false;
			}
		}
//FIXME move password algorithm out of here!!!
		$q = 'UPDATE tblUsers SET userPass="'.sha1( $_id.sha1($key).sha1($_pwd1) ).'" WHERE userId='.$_id;
		$db->update($q);
		return true;
	}

	function activeTime($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$db->update('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$_id);
	}

	function loginTime($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$db->update('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$_id);
	}

	function logoutTime($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$db->update('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$_id);
	}

	function validLogin($username, $password)
	{
		global $db;
 		$q = 'SELECT * FROM tblUsers WHERE userName="'.$db->escape($username).'" AND userPass="'.$db->escape($password).'" AND timeDeleted IS NULL';
		return $db->getOneRow($q);
	}

	/**
	 * Looks up a user id by name. Does not take into account deleted users
	 */
	function getId($_name)
	{
		global $db;
		$q = 'SELECT userId FROM tblUsers WHERE userName="'.$db->escape($_name).'" AND timeDeleted IS NULL';
		return $db->getOneItem($q);
	}

	/**
	 * Looks up a username by id
	 */
	function getName($_id)
	{
		global $db, $h;

		if (!is_numeric($_id) || !$_id) return false;
		if ($_id == $h->session->id) return $h->session->username;

		$q = 'SELECT userName FROM tblUsers WHERE userId='.$_id;
		return $db->getOneItem($q);
	}

	/**
	 * Returns number of users online
	 */
	function onlineCnt()
	{
		global $db, $h;

		$q  = 'SELECT COUNT(*) FROM tblUsers WHERE timeDeleted IS NULL';
		$q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$h->session->online_timeout.' SECOND)';
		return $db->getOneItem($q);
	}

	/**
	 * Returns array of all users online
	 */
	function allOnline($_limit = '')
	{
		global $db, $h;

		$q  = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL';
		$q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$h->session->online_timeout.' SECOND)';
		$q .= ' ORDER BY timeLastActive DESC';
		if (!empty($_limit)) $q .= $_limit;
		return $db->getArray($q);
	}


























	/**
	 * Get a list of all users with birthday at $date
	 */
	function getUsersBornAtDate($date)
	{
		global $db;

		$type = getUserdataFieldIdByType(USERDATA_TYPE_BIRTHDATE_SWE);

		$q  = 'SELECT ownerId FROM tblSettings WHERE settingName = '.$type.' AND ';
		$q .= 'day(settingValue) = day("'.$date.'") AND month(settingValue) = month("'.$date.'")';
 		return $db->getArray($q);
	}

	/**
	 * Get the number of logins during the specified time period
	 */
	function getLoginCountPeriod($dateStart, $dateStop)
	{
		global $db;

		$q = 'SELECT count(userId) AS cnt FROM tblLogins WHERE timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
		return $db->getOneItem($q);
	}

	/**
	 * Get the number of distinct logins during the specified time period
	 */
	function getDistinctLoginCountPeriod($dateStart, $dateStop)
	{
		global $db;

		$q = 'SELECT count(distinct(userId)) AS cnt FROM tblLogins WHERE timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
		return $db->getOneItem($q);
	}

	/**
	 * Get the number of new users registred during the specified time period
	 */
	function getNewCountPeriod($dateStart, $dateStop)
	{
		global $db;

		$q = 'SELECT count(userId) AS cnt FROM tblUsers WHERE timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
		return $db->getOneItem($q);
	}

	/**
	 * Looks up a users latest logintime by id
	 */
	function getLogintime($_id)
	{
		global $db, $session;
		if (!is_numeric($_id) || !$_id) return false;

		$q = 'SELECT timeLastLogin FROM tblUsers WHERE userId='.$_id;
		return $db->getOneItem($q);
	}

	/**
	 * Return the number of logins by user
	 */
	function loginCnt($_id)
	{
		global $db, $session;
		if (!is_numeric($_id) || !$_id) return false;

		$q = 'SELECT COUNT(*) FROM tblLogins WHERE userId='.$_id;
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
			case USERLEVEL_NORMAL: $msg = $session->username.' has reduced your usermode to normal member.'; break;
			case USERLEVEL_WEBMASTER: $msg = $session->username.' has granted you webmaster rights.'; break;
			case USERLEVEL_ADMIN: $msg = $session->username.' has granted you admin rights.'; break;
			case USERLEVEL_SUPERADMIN: $msg = $session->username.' has granted you super admin rights.'; break;
		}
		sendMessage($_id, 'System message', $msg);

		$session->log('Changed usermode for '.Users::getName($_id).' to '.$_mode);	//FIXME lookup from Session->userModes
		return true;
	}

	/**
	 * Returns the $_limit last users logged in, ordered by the latest logins first
	 */
	function lastLoggedIn($_limit = 50)
	{
		global $db, $session;
		if (!is_numeric($_limit)) return false;

		$q  = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL ORDER BY timeLastLogin DESC';
		$q .= ' LIMIT 0,'.$_limit;
		return $db->getArray($q);
	}

	/**
	 * Returns total number of users (excluding deleted ones)
	 */
	function cnt($_mode = 0, $_namefilter = '')
	{
		global $db;
		if (!is_numeric($_mode)) return false;

		$q = 'SELECT COUNT(*) FROM tblUsers';
		$q .= ' WHERE timeDeleted IS NULL';
		if ($_mode) $q .= ' AND userMode='.$_mode;
		if ($_namefilter) $q .= ' AND userName LIKE "%'.$db->escape($_namefilter).'%"';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of webmasters
	 */
	function webmasterCnt()
	{
		return Users::cnt(USERLEVEL_WEBMASTER);
	}

	/**
	 * Returns total number of admins
	 */
	function adminCnt()
	{
		return Users::cnt(USERLEVEL_ADMIN);
	}

	/**
	 * Returns total number of super admins
	 */
	function superAdminCnt()
	{
		return Users::cnt(USERLEVEL_SUPERADMIN);
	}

	/**
	 * Admin function used by admin_list_users.php
	 *
	 * @param $_mode usermode
	 * @param $_limit sql LIMIT (from makePager)
	 * @param $_namefilter partial username matching
	 */
	function getUsers($_mode = 0, $_limit = '', $_namefilter = '')
	{
		global $db;

		if (!is_numeric($_mode)) return false;

		$q = 'SELECT * FROM tblUsers';
		$q .= ' WHERE timeDeleted IS NULL';
		if ($_mode) $q .= ' AND userMode='.$_mode;
		if ($_namefilter) $q .= ' AND userName LIKE "%'.$db->escape($_namefilter).'%"';
		if ($_limit) $q .= ' '.$_limit;
		return $db->getArray($q);
	}

	/**
	 * Returns a random user id
	 */
	function getRandomUserId()
	{
		global $db, $h;

		$q  = 'SELECT userId FROM tblUsers';
		$q .= ' WHERE userName IS NOT NULL AND timeDeleted IS NULL';
		if ($h->session->id) $q .= ' AND userId!='.$h->session->id;
		$q .= ' ORDER BY RAND() LIMIT 1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns userId of first match of username contains $phrase, for quick search
	 */
	function searchUsernameContains($phrase)
	{
		global $db;

		$q  = 'SELECT userId FROM tblUsers';
		$q .= ' WHERE timeDeleted IS NULL';
		$q .= ' AND LOWER(userName) LIKE LOWER("%'.$db->escape($phrase).'%") LIMIT 1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns all usernames beginning with $phrase
	 */
	function searchUsernameBeginsWith($phrase)
	{
		global $db;

		$q  = 'SELECT userId,userName FROM tblUsers';
		$q .= ' WHERE timeDeleted IS NULL';
		$q .= ' AND LOWER(userName) LIKE LOWER("'.$db->escape($phrase).'%")';
		$q .= ' ORDER BY userName ASC';
		return $db->getArray($q);
	}

	/**
	 * Completely deletes this user and all associated data from the database
	 */
	function delete($_id)
	{
		global $db, $session;
		if (!$session->isSuperAdmin || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblUsers WHERE userId='.$_id;
		$db->delete($q);

		deleteSettings(SETTING_USERDATA, 0, $_id);
		deleteAllContacts($_id);
		//FIXME delete other traces too
	}

	/**
	 * Marks specified user as "deleted"
	 */
	function removeUser($userId)
	{
		global $db;
		if (!is_numeric($userId)) return false;

		$q = 'UPDATE tblUsers SET timeDeleted=NOW() WHERE userId='.$userId;
		$db->update($q);
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
	 * Admin function used by admin_ip.php to show information about a users IP-addressess
	 */
	function getIPByUser($user)
	{
		global $db;

		if (!is_numeric($user)) return false;

		$q  = 'SELECT distinct(IP) AS IP, timeCreated AS time FROM tblLogins ';
		$q .= 'WHERE userId = '.$user.' GROUP BY IP';
		return $db->getArray($q);
	}

	/**
	 * Generates a link to user's page
	 */
	function link($id, $name = '', $class = '')
	{
		if (!$id) return t('System message');
		if (!$name) $name = Users::getName($id);
		if (!$name) return t('User not found');

		return '<a '.($class?' class="'.$class.'"':'').'href="user.php?id='.$id.'">'.$name.'</a>';
	}

	/**
	 * Generates a clickable thumbnail to user's page
	 */
	function linkThumb($id, $alt = '', $w = 50, $h = 50)
	{
		if (!$id) return t('System message');
		if (!$alt) $alt = Users::getName($id);
		if (!$alt) return t('User deleted');

		$out  = '<a href="user.php?id='.$id.'">';
		$out .= Users::thumb($id, $alt, $w, $h).'</a>';
		return $out;
	}

	/**
 	 * Generates a thumbnail of user's presentation image
 	 */
	function thumb($id, $alt = '', $w = 50, $h = 50)
	{
		$fileId = loadUserdataImage($id);
		return showThumb($fileId, $alt, $w, $h);
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
				logVisit(VISIT_USERPAGE, $userId);
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
		global $h;

		if (isset($_POST['c'])) {

			$list = Users::getSearchResult($_POST);

			if (!empty($_POST['c'])) echo t('Search result for').' "'.$_POST['c'].'", ';
			else echo t('Custom search result').', ';

			//FIXME: rename custom function name, keep default in functions_defaults (?)
			if (function_exists('showCustomSearchResult')) { // call project specified search presentation function
				return showCustomSearchResult($list);
			}

			echo (count($list)!=1?count($list).t(' hits'):t('1 hit'));
			echo '<br/><br/>';

			for ($i=0; $i<count($list); $i++) {
				echo Users::link($list[$i]['userId'], $list[$i]['userName']).'<br/>';
			}
			echo '<br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">'.t('New search').'</a><br/>';

			echo '<br/>';
			return;
		}

		if (isset($_GET['l']) && $_GET['l']) {
			/* List all usernames starting with letter 'l' */

			$list = Users::searchUsernameBeginsWith($_GET['l']);

			echo t('Usernames beginning with').' "'.$_GET['l'].'", ';

			echo (count($list)!=1?count($list).t(' hits'):t('1 hit'));
			echo '<br/><br/>';

			for ($i=0; $i<count($list); $i++) {
				echo Users::link($list[$i]['userId'], $list[$i]['userName']).'<br/>';
			}

			echo '<br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">'.t('New search').'</a><br/>';
			return;
		}

		echo t('Show usernames beginning with').': ';
		for ($i=ord('A'); $i<=ord('Z'); $i++) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?l='.chr($i).'">'.chr($i).'</a> ';
		}
		echo '<br/><br/>';

		echo'<form name="src" method="post" action="'.$_SERVER['PHP_SELF'].'">';

		echo t('Free-text').': ';
		echo '<input type="text" name="c" maxlength="20" size="20"/><br/>';

		$list = getUserdataFields();
		echo '<table>';
		foreach ($list as $row) {
			if ($row['private'] && !$h->session->isAdmin) continue;

			echo '<tr'.($row['private']?' class="critical"':'').'>';
			echo getUserdataSearch($row);
			echo '</tr>';
			if ($row['private']) {
				echo '<tr><td colspan="2">';
				echo '<div class="critical"> * '.t('This field can only be used in searches by admins').'</div>';
				echo '</td></tr>';
			}
		}
		echo '</table>';

		echo '<input type="submit" class="button" value="'.t('Search').'"/>';
		echo '</form>';
		echo '<script type="text/javascript">if (document.src) document.src.c.focus();</script>';
	}

	/**
	 * Used by Users::search()
	 *
	 * Data is $_POST and can contain irrelevant info!
	 */
	function getSearchResult($data)
	{
		global $db, $h;

		$criteria = 0;
		if (isset($data['c'])) {
			$data['c'] = trim($data['c']);
			$criteria = substr($data['c'], 0, 30); //only allow up to 30 characters in search free-text
			$criteria = $db->escape($data['c']);
		}

		$q  = 'SELECT t1.userId,t1.userName FROM tblUsers AS t1 ';

		// $criteria matches what's in all textarea & textfields
		if ($criteria) {
			$q .= 'LEFT JOIN tblSettings AS n1 ON (t1.userId=n1.ownerId AND n1.settingType='.SETTING_USERDATA.') ';
		}

		$list = getUserdataFields();

		$start = 2; //autogenerated LEFT JOIN tables will be called n1, n2 etc.

		// Add one INNER JOIN for each parameter we want to search for
		foreach ($list as $row) {
			if (!$h->session->isAdmin && $row['private']) continue;
			if ($row['fieldType'] == USERDATA_TYPE_LOCATION_SWE && !empty($data['search_loc_city'])) {
				$q .= 'LEFT JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId AND n'.$start.'.settingName="city" AND n'.$start.'.settingType='.SETTING_USERDATA.') ';
				$start++;
			} else if ($row['fieldType'] == USERDATA_TYPE_LOCATION_SWE && !empty($data['search_loc_region'])) {
				$q .= 'LEFT JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId AND n'.$start.'.settingName="region" AND n'.$start.'.settingType='.SETTING_USERDATA.') ';
				$start++;
			} else if (!empty($data['userdata_'.$row['fieldId']])) {
				$q .= 'LEFT JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId AND n'.$start.'.settingName="'.$row['fieldId'].'" AND n'.$start.'.settingType='.SETTING_USERDATA.') ';
				$start++;
			}
		}

		$q .= 'WHERE t1.timeDeleted IS NULL ';
		if ($criteria) { //free-text search
			$q .= 'AND (((n1.settingType='.USERDATA_TYPE_TEXT.' OR n1.settingType='.USERDATA_TYPE_TEXTAREA.') ';
			$q .= 'AND LOWER(n1.settingValue) LIKE LOWER("%'.$criteria.'%")) ';
			$q .= 'OR LOWER(t1.userName) LIKE LOWER("%'.$criteria.'%")) ';
			$x = 1;
		}

		$start = 2; //autogenerated INNER JOIN tables will be called n1, n2 etc.

		// Find the userdata fields the user searched for
		foreach ($list as $row) {
			if (!$h->session->isAdmin && $row['private']) continue;
			if (!empty($data['userdata_'.$row['fieldId']]) || (!empty($data['search_loc_region']) || !empty($data['search_loc_city']))) {
				if ($start > 1) { // n1 is always created!
					switch ($row['fieldType']) {
						case USERDATA_TYPE_IMAGE:
							if (!empty($data['userdata_'.$row['fieldId']])) {
//								if (isset($x)) $q .= 'AND ';
								$q .= 'AND (n'.$start.'.settingValue IS NOT NULL AND n'.$start.'.settingValue != 0) ';
								$start++;
								$x = 1;
							}
							break;

						case USERDATA_TYPE_LOCATION_SWE:
							if (!empty($data['search_loc_city']) && is_numeric($data['search_loc_city'])) {
//								if (isset($x)) $q .= 'AND ';
								$q .= 'AND (n'.$start.'.settingValue="'.$data['search_loc_city'].'") ';
								$start++;
								$x = 1;
							} else if (!empty($data['search_loc_region']) && is_numeric($data['search_loc_region'])) {
//								if (isset($x)) $q .= 'AND ';
								$q .= 'AND (n'.$start.'.settingValue="'.$data['search_loc_region'].'") ';
								$start++;
								$x = 1;
							}
							break;

						case USERDATA_TYPE_BIRTHDATE:
						case USERDATA_TYPE_BIRTHDATE_SWE:
							if (empty($data['userdata_'.$row['fieldId']])) break;
							$rng = explode('_', $data['userdata_'.$row['fieldId']]);
							if (count($rng) != 2) break;
							$from = $rng[0];
							$to = $rng[1];
							if (!$from) $from = '0000-00-00';
							if (!$to) $to = '9999-12-31';
//							if (isset($x)) $q .= 'AND ';
							$q .= 'AND (n'.$start.'.settingValue BETWEEN "'.$db->escape($from).'" AND "'.$db->escape($to).'") ';
							$start++;
							$x = 1;
							break;

						default:
							if (!empty($data['userdata_'.$row['fieldId']])) {
//								if (isset($x)) $q .= 'AND ';
								$q .= 'AND (n'.$start.'.settingValue="'.$data['userdata_'.$row['fieldId']].'") ';
								$start++;
								$x = 1;
							}
							break;
					}
				}
			}
		}

		if (!isset($x)) return array();

		$q .= 'GROUP BY t1.userId ';
		$q .= 'ORDER BY t1.userName';

		return $db->getArray($q);
	}

	/**
	 * Adds a entry in tblSettings marking this user account as activated
	 *
	 * @param $_id user id
	 */
	function activate($_id)
	{
		if (!is_numeric($_id)) return false;

		saveSetting(SETTING_USERDATA, 0, $_id, 'activated', true);
	}

	/**
	 * Checks if user is activated, returns true/false
	 *
	 * @param $_id user id
	 */
	function isActivated($_id)
	{
		if (!is_numeric($_id)) return false;

		if (loadSetting(SETTING_USERDATA, 0, $_id, 'activated')) return true;
		return false;
	}

	/**
	 * Checks if user exists (and is not deleted), returns true/false
	 *
	 * @param $_id user id
	 */
	function exists($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT COUNT(*) FROM tblUsers WHERE userId='.$_id.' AND timeDeleted IS NULL';
		if ($db->getOneItem($q)) return true;
		return false;
	}

	/**
	 * Checks if user is online, returns true/false
	 *
	 * @param $_id user id
	 */
	function isOnline($_id)
	{
		global $db, $h;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT userId FROM tblUsers WHERE userId = '.$_id.' AND timeDeleted IS NULL AND timeLastActive>=DATE_SUB(NOW(),INTERVAL '.$h->session->online_timeout.' SECOND) LIMIT 1';
		if ($db->getOneItem($q)) return true;

		return false;
	}

}
?>
