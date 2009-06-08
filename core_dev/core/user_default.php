<?php
/**
 * $Id$
 *
 * Default user class, using tblUsers
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//TODO: implement remove() & unregister()

require_once('user_base.php');
require_once('class.Users.php');

require_once('functions_userdata.php');	//for verifyRequiredUserdataFields()
require_once('atom_moderation.php');	//for isReservedUsername()

define('USERLEVEL_NORMAL',		0);
define('USERLEVEL_WEBMASTER',	1);
define('USERLEVEL_ADMIN',		2);
define('USERLEVEL_SUPERADMIN',	3);

class user_default extends user_base
{
	var $reserved_usercheck = true;		///< check if username is listed as reserved username, requires tblStopwords
	var $userdata = true; 				///< shall we use tblUserdata for required userdata fields?

	var $minlen_username = 3;			///< minimum length for valid usernames
	var $minlen_password = 4;			///< minimum length for valid passwords

	function __construct($conf = array())
	{
		if (isset($conf['minlen_username'])) $this->minlen_username = $conf['minlen_username'];
		if (isset($conf['minlen_password'])) $this->minlen_password = $conf['minlen_password'];
		if (isset($conf['reserved_usercheck'])) $this->reserved_usercheck = $conf['reserved_usercheck'];
		if (isset($conf['userdata'])) $this->userdata = $conf['userdata'];
	}

	/**
	 * Creates a tblUsers entry without username or password
	 */
	function reserve()
	{
		return Users::reserveId();
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
		if (!is_numeric($_mode) || !is_numeric($newUserId)) return false;

		if ($username != trim($username)) return t('Username contains invalid spaces');

		if (strlen($username) < $this->minlen_username) return t('Username must be at least').' '.$this->minlen_username.' '.t('characters long');
		if (strlen($password1) < $this->minlen_password) return t('Password must be at least').' '.$this->minlen_password.' '.t('characters long');
		if ($password1 != $password2) return t('The passwords doesnt match');

		if ($this->reserved_usercheck && isReservedUsername($username)) return t('Username is not allowed');

		//Checks if email was required, and if so if it was correctly entered
		if ($this->userdata) {
			$chk = verifyRequiredUserdataFields();
			if ($chk !== true) return $chk;
		}

		if (Users::cnt()) {
			if (Users::getId($username)) return t('Username already exists');
		} else {
			//No users exists, give this user superadmin status
			$_mode = USERLEVEL_SUPERADMIN;
		}

		if (!$newUserId) {
			$newUserId = Users::registerUser($username, $_mode);
		} else {
			Users::updateUser($newUserId, $username, $_mode);
		}

		Users::setPassword($newUserId, $password1, $password2);

		dp('Registered user: '.$username);

		//Stores the additional data from the userdata fields that's required at registration
		if ($this->userdata) {
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
