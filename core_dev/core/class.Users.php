<?
/**
 * $Id$
 *
 * Users class
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_visits.php');
require_once('functions_locale.php');	//for translations

$config['user']['log_visitors'] = true;	

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
			case 0: $msg = $session->username.' has reduced your usermode to normal member.'; break;
			case 1: $msg = $session->username.' has granted you admin rights.'; break;
			case 2: $msg = $session->username.' has granted you super admin rights.'; break;
		}
		sendMessage($_id, 'System message', $msg);

		$session->log('Changed usermode for '.Users::getName($_id).' to '.$_mode);
		return true;
	}

	/**
	 * Sets a new password for the user
	 */
	function setPassword($_id, $_pwd1, $_pwd2)
	{
		global $db, $session, $auth;
		if (!is_numeric($_id)) return false;

		if (strlen($_pwd1) < 4) {
			$session->error = 'Password must be at least 4 characters long';
			return false;
		}

		if ($_pwd1 != $_pwd2) {
			$session->error = 'The passwords doesnt match';
			return false;
		}

		$q = 'UPDATE tblUsers SET userPass="'.sha1( sha1($auth->sha1_key).sha1($_pwd1) ).'" WHERE userId='.$_id;
		$db->update($q);
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
	function onlineCnt()
	{
		global $db, $session;

		$q  = 'SELECT COUNT(*) FROM tblUsers WHERE timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of users
	 */
	function cnt()
	{
		global $db;

		$q = 'SELECT COUNT(*) FROM tblUsers';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of admins
	 */
	function adminCnt()
	{
		global $db;

		$q = 'SELECT COUNT(*) FROM tblUsers WHERE userMode=1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns total number of super admins
	 */
	function superAdminCnt()
	{
		global $db;

		$q = 'SELECT COUNT(*) FROM tblUsers WHERE userMode=2';
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
		global $db, $session;

		$q  = 'SELECT userId FROM tblUsers ';
		if ($session->id) $q .= 'WHERE userId!='.$session->id.' ';
		$q .= 'ORDER BY RAND() LIMIT 1';
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
	 * Completely deletes this user and all associated data from the database
	 */
	function delete($_id)
	{
		global $db, $session;
		if (!$session->isSuperAdmin || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblUsers WHERE userId='.$_id;
		$db->delete($q);

		deleteSettings(SETTING_USERDATA, $_id);
		deleteAllContacts($_id);
		//FIXME delete other traces too
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
		if (!$name) return 'user not found';

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
		
		$pic_id = loadUserdataImage($id);

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
		global $session;

		if (isset($_POST['c'])) {

		$list = Users::getSearchResult($_POST);

			if (!empty($_POST['c'])) echo t('Search result for').' "'.$_POST['c'].'", ';
			else echo t('Custom search result').', ';

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
				echo Users::link($list[$i]['userId'], $list[$i]['userName']).'<br>';
			}

			echo '<br>';
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
		foreach ($list as $row) {
			if ($row['private']) {
			 	if (!$session->isAdmin) continue;
				echo '<div class="critical">';
			}
			echo getUserdataSearch($row).'<br/>';
			if ($row['private']) echo '<br/>'.t('This field can only be used in searches by admins').'</div>';
		}

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
		global $db, $session;
		
		$data['c'] = trim($data['c']);
		$criteria = substr($data['c'], 0, 30); //only allow up to 30 characters in search free-text
		$criteria = $db->escape($data['c']);

		$q  = 'SELECT t1.userId,t1.userName FROM tblUsers AS t1 ';

		// $criteria matches what's in all textarea & textfields
		if ($criteria) {
			$q .= 'LEFT JOIN tblSettings AS n1 ON (t1.userId=n1.ownerId AND n1.settingType='.SETTING_USERDATA.') ';
			$q .= 'LEFT JOIN tblUserdata AS t2 ON (n1.settingName=t2.fieldId) ';
		}

		$list = getUserdataFields();

		$start = 2; //autogenerated LEFT JOIN tables will be called n1, n2 etc.

		// Add one INNER JOIN for each parameter we want to search for
		foreach ($list as $row) {
			if (!empty($data['userdata_'.$row['fieldId'] ])) {
				$q .= 'LEFT JOIN tblSettings AS n'.$start.' ON (t1.userId=n'.$start.'.ownerId AND n'.$start.'.settingName="'.$row['fieldId'].'" AND n'.$start.'.settingType='.SETTING_USERDATA.') ';
				$start++;
			}
		}

		$q .= 'WHERE ';
		if ($criteria) { //free-text search
			$q .= '((n1.settingType='.USERDATA_TYPE_TEXT.' OR n1.settingType='.USERDATA_TYPE_TEXTAREA.') ';
			$q .= 'AND LOWER(n1.settingValue) LIKE LOWER("%'.$criteria.'%") AND t2.private!=1) ';
			$q .= 'OR LOWER(t1.userName) LIKE LOWER("%'.$criteria.'%") ';
			$x = 1;
		}

		$start = 2; //autogenerated INNER JOIN tables will be called n1, n2 etc.

		// Find the userdata fields the user searched for
		foreach ($list as $row) {
			if (!empty($data['userdata_'.$row['fieldId']])) {
				if (isset($x)) $q .= 'AND ';
				if ($start > 1) { // n1 is always created!
					if ($row['fieldType'] == USERDATA_TYPE_IMAGE) {
						$q .= '(n'.$start.'.settingValue IS NOT NULL) ';
					} else {
						$q .= '(n'.$start.'.settingValue="'.$data['userdata_'.$row['fieldId']].'") ';
					}
				}
				$start++;
				$x = 1;
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
	 * \param $_id user id
	 */
	function activate($_id)
	{
		if (!is_numeric($_id)) return false;

		saveSetting(SETTING_USERDATA, $_id, 'activated', true);
	}

	/**
	 * Checks if user is activated, returns true/false
	 *
	 * \param $_id user id
	 */
	function isActivated($_id)
	{
		if (!is_numeric($_id)) return false;

		if (loadSetting(SETTING_USERDATA, $_id, 'activated')) return true;
		return false;
	}

}
?>
