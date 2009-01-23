<?php
/**
 * $Id$
 *
 * Default authentication class. Uses core_dev's own tblUsers
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//TODO: handleForgotPassword(): use output_smtp.php instead!

require_once('auth_base.php');
require_once('design_auth.php');		//default functions for auth xhtml forms
require_once('class.Users.php');

require_once('atom_events.php');		//for event logging
require_once('atom_ip.php');			//for IPv4_to_GeoIP()
require_once('atom_blocks.php');		//for isBlocked()
require_once('atom_activation.php');	//for generateActivationCode()

class auth_default extends auth_base
{
	var $driver = 'default';

	var $error = '';	///< contains last error message, if any

	var $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	///< used to further encode sha1 passwords, to make rainbow table attacks harder

	var $allow_login = true;				///< set to false to only let superadmins log in to the site
	var $allow_registration = true;		///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
	var $mail_activate = false;			///< does account registration require email activation?
	var $mail_error = false;				///< will be set to true if there was problems sending out email

	var $activation_sent = false;		///< internal. true if mail activation has been sent
	var $resetpwd_sent = false;			///< internal. true if mail for password reset has been sent

	var $check_ip = true;				///< client will be logged out if client ip is changed during the session
	var $ip = 0;						///< IP of user

	function __construct($conf = array())
	{
		if (isset($conf['sha1_key'])) $this->sha1_key = $conf['sha1_key'];
		if (isset($conf['allow_login'])) $this->allow_login = $conf['allow_login'];
		if (isset($conf['allow_registration'])) $this->allow_registration = $conf['allow_registration'];
		if (isset($conf['mail_activate'])) $this->mail_activate = $conf['mail_activate'];

		if (isset($conf['check_ip'])) $this->check_ip = $conf['check_ip'];

		if (!isset($_SESSION['user_agent'])) $_SESSION['user_agent'] = '';

		$this->ip = &$_SESSION['ip'];
		$this->user_agent = &$_SESSION['user_agent'];

		if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) {
			$this->ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
		}
	}

	/**
	 * Handles logins
	 *
	 * @param $username
	 * @param $password
	 * @return true on success
	 */
	function login($username, $password)
	{
		$data = $this->validLogin($username, $password);

		if (!$data) {
			$this->error = t('Login failed');
			dp('Failed login attempt: username '.$username, LOGLEVEL_WARNING);
			return false;
		}

		if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
			if ($this->mail_activate && !Users::isActivated($data['userId'])) {
				$this->error = t('This account has not yet been activated.');
				return false;
			}

			if (!$this->allow_login) {
				$this->error = t('Logins currently not allowed.');
				return false;
			}

			$blocked = isBlocked(BLOCK_USERID, $data['userId']);
			if ($blocked) {
				$this->error = t('Account blocked');
				dp('Login attempt from blocked user: username '.$username, LOGLEVEL_WARNING);
				return false;
			}
		}
		return $data;
	}

	/**
	 * Logs out the user
	 */
	function logout($userId)
	{
		Users::logoutTime($userId);
		addEvent(EVENT_USER_LOGOUT, 0, $userId);
	}

	/**
	 * Checks if this is a valid login
	 *
	 * @return if valid login, return user data, else false
	 */
	function validLogin($username, $password)
	{
		$id = Users::getId($username);
		$enc_password = sha1( $id.sha1($this->sha1_key).sha1($password) );

		return Users::validLogin($username, $enc_password);
	}








	/**
	 * Looks up user supplied email address / alias and generates a mail for them if needed
	 *
	 * @param $email email address
	 */
	function handleForgotPassword($email)
	{
		global $config;

//FIXME use output_smtp.php instead!
die('handleForgotPassword() needs fixing!');
		$email = trim($email);
		if (strpos($email, '@')) {
			if (!ValidEmail($email)) return false;
			$_id = findUserByEmail($email);
		} else {
			//find user by alias
			$_id = Users::getId($email);
		}
		if (!$_id) {
			$session->error = t('Invalid email address or username');
			return false;
		}

		$email = loadUserdataEmail($_id);

		$code = generateActivationCode(ACTIVATE_CHANGE_PWD, 10000000, 99999999);
		createActivation(ACTIVATE_CHANGE_PWD, $code, $_id);

		$subj  = t('Forgot password');

		$pattern = array('/__USERNAME__/', '/__IP__/', '/__URL__/', '/__EXPIRETIME__/');
		$replacement = array(
			Users::getName($_id),
			$_SERVER['REMOTE_ADDR'],
			$config['app']['full_url']."reset_password.php?id=".$_id."&code=".$code,
			shortTimePeriod($config['activate']['expire_time_email'])
		);
		$msg = preg_replace($pattern, $replacement, $this->mail_password_msg);

		if (!$this->SmtpSend($email, $subj, $msg)) {
			removeActivation(ACTIVATE_CHANGE_PWD, $code);
			$session->error = t('Problems sending mail');
			return false;
		}

		$this->resetpwd_sent = true;
		return true;
	}

}
?>
