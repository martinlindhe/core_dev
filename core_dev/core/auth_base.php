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
	/**
	 * Constructor
	 *
	 * @param $auth_conf auth configuration
	 */
	function __construct($conf = array())
	{

	}

	/**
	 * Attempts to register a user
	 *
	 * @param $username
	 * @param $password1
	 * @param $password2
	 * @param $userMode
	 */
	//abstract function registerUser($username, $password1, $password2, $userMode = 0);

	//FIXME abstract func. unregisterUser()

	/**
	 * Displays built in login form
	 */
	abstract function showLoginForm();

	/**
	 * Handles login, logout & register user requests
	 */
	abstract function handleAuthEvents();

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
