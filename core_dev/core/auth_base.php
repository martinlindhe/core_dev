<?php
/**
 * $Id$
 *
 * Skeleton for authentication modules
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

abstract class auth_base
{
	public $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	///< used to further encode sha1 passwords, to make rainbow table attacks harder

	public $allow_login = true;				///< set to false to only let superadmins log in to the site
	public $allow_registration = true;		///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
	public $reserved_usercheck = true;		///< check if username is listed as reserved username, requires tblStopwords
	public $userdata = true; 				///< shall we use tblUserdata for required userdata fields?
	public $mail_activate = false;			///< does account registration require email activation?
	public $mail_error = false;				///< will be set to true if there was problems sending out email

	public $activation_sent = false;		///< internal. true if mail activation has been sent
	public $resetpwd_sent = false;			///< internal. true if mail for password reset has been sent

	public $minlen_username = 3;			///< minimum length for valid usernames
	public $minlen_password = 4;			///< minimum length for valid passwords

	/**
	 * Constructor
	 *
	 * @param $auth_conf auth configuration
	 */
	function __construct($conf = array())
	{
		if (isset($conf['sha1_key'])) $this->sha1_key = $conf['sha1_key'];
		if (isset($conf['allow_login'])) $this->allow_login = $conf['allow_login'];
		if (isset($conf['allow_registration'])) $this->allow_registration = $conf['allow_registration'];
		if (isset($conf['reserved_usercheck'])) $this->reserved_usercheck = $conf['reserved_usercheck'];
		if (isset($conf['userdata'])) $this->userdata = $conf['userdata'];
		if (isset($conf['mail_activate'])) $this->mail_activate = $conf['mail_activate'];

		if (isset($conf['minlen_username'])) $this->minlen_username = $conf['minlen_username'];
		if (isset($conf['minlen_password'])) $this->minlen_password = $conf['minlen_password'];
	}

	/**
	 * Attempts to register a user
	 *
	 * @param $username
	 * @param $password1
	 * @param $password2
	 * @param $userMode
	 */
	abstract function registerUser($username, $password1, $password2, $userMode = 0);

	//FIXME abstract func. unregisterUser()

	/**
	 * Displays built in login form
	 */
	abstract function showLoginForm();

	/**
	 * Handles login, logout & register user requests
	 */
	abstract function handleAuthEvents($session, $user);

	/**
	 * Looks up user supplied email address / alias and generates a mail for them if needed
	 *
	 * @param $email email address
	 */
	abstract function handleForgotPassword($email);

	/**
	 * Reset user's password
	 *
	 * @param $_id user id
	 * @param $_code reset code
	 * @return true on success
	 */
	abstract function resetPassword($_id, $_code);

}
?>
