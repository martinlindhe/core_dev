<?php
/**
 * $Id$
 *
 * Default user class, using tblUsers
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('user_base.php');

//TODO: implement remove() & unregister()
//TODO: define required functions in user_base.php

define('USERLEVEL_NORMAL',		0);
define('USERLEVEL_WEBMASTER',	1);
define('USERLEVEL_ADMIN',		2);
define('USERLEVEL_SUPERADMIN',	3);

class user_default extends user_base
{
	function __construct($conf = array())
	{
	}

	/**
	 * Creates a tblUsers entry without username or password
	 */
	function reserve()
	{
		$q = 'INSERT INTO tblUsers SET userMode=0';
		return $this->db->insert($q);
	}

	/**
	 * Register new user in the database
	 *
	 * @param $username user name
	 * @param $password1 password
	 * @param $password2 password (repeat)
	 * @param $_mode user mode
	 * @param $newUserId supply reserved user id. if not supplied, a new user id will be allocated
	 * @return the user ID of the newly created user
	 */
	function register($username, $password1, $password2, $_mode = USERLEVEL_NORMAL, $newUserId = 0)
	{
		global $session;
		if (!is_numeric($_mode) || !is_numeric($newUserId)) return false;

		if ($username != trim($username)) return t('Username contains invalid spaces');

		if (($db->escape($username) != $username) || ($db->escape($password1) != $password1)) {
			//if someone tries to enter ' or " etc as username/password letters
			//with this check, we dont need to encode the strings for use in sql query
			return t('Username or password contains invalid characters');
		}

		if (strlen($username) < $this->minlen_username) return t('Username must be at least').' '.$this->minlen_username.' '.t('characters long');
		if (strlen($password1) < $this->minlen_password) return t('Password must be at least').' '.$this->minlen_password.' '.t('characters long');
		if ($password1 != $password2) return t('The passwords doesnt match');

		if (!$session->isSuperAdmin) {
			if ($this->reserved_usercheck && isReservedUsername($username)) return t('Username is not allowed');

			//Checks if email was required, and if so if it was correctly entered
			if ($this->userdata) {
				$chk = verifyRequiredUserdataFields();
				if ($chk !== true) return $chk;
			}
		}

		if (Users::cnt()) {
			if (Users::getId($username)) return t('Username already exists');
		} else {
			//No users exists, give this user superadmin status
			$_mode = USERLEVEL_SUPERADMIN;
		}

		if (!$newUserId) {
			$q = 'INSERT INTO tblUsers SET userName="'.$username.'",userMode='.$_mode.',timeCreated=NOW()';
			$newUserId = $this->db->insert($q);
		} else {
			$q = 'UPDATE tblUsers SET userName="'.$username.'",userMode='.$_mode.',timeCreated=NOW() WHERE userId='.$newUserId;
			$this->db->update($q);
		}

		Users::setPassword($newUserId, $password1, $password1, $this->sha1_key);

		$session->log('Registered user <b>'.$username.'</b>');

		//Stores the additional data from the userdata fields that's required at registration
		if (!$session->isSuperAdmin && $this->userdata) {
			handleRequiredUserdataFields($newUserId);
		}

		return $newUserId;
	}

	/**
	 * Marks user as unregistered, keeping their username reserved
	 */
	function unregister()
	{
		die('FIXME IMPLEMENT unregister()!');
	}

	/**
	 * Removes a user from the database
	 */
	function remove()
	{
		die('FIXME IMPLEMENT remove()!');
	}

}

?>
