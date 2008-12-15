<?php
/**
 * $Id$
 *
 * Skeleton for authentication modules
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//TODO libapache2-mod-auth-openid module

require_once('atom_activation.php');		//for activation
require_once('ext/class.phpmailer.php');	//for outgoing mail. FIXME look for bsd-compatible mailer
require_once('functions_ip.php');			//for IPv4_to_GeoIP()
require_once('atom_blocks.php');			//for isBlocked()

abstract class Auth_Base
{
	private $check_ip = true;				///< client will be logged out if client ip is changed during the session
	private $check_useragent = false;		///< keeps track if the client user agent string changes during the session
	private $ip = false;					///< IP of user

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

	//SMTP out settings
	public $smtp_host = 'smtp.example.com';
	public $smtp_username = '';
	public $smtp_password = '';

	public $mail_from = 'noreply@example.com';
	public $mail_from_name = 'core_dev';

	public $mail_activate_msg =
		"Hello. Someone (probably you) registered an account from IP __IP__

Username: __USERNAME__
Activation code: __CODE__

Follow this link to activate your account:
__URL__

The link will expire in __EXPIRETIME__";

	public $mail_password_msg =
		"Hello. Someone (probably you) asked for a password reset procedure from IP __IP__

Registered username: __USERNAME__

Follow this link to set a new password:
__URL__

The link will expire in __EXPIRETIME__";

	/**
	 * Constructor
	 *
	 * @param $auth_conf auth configuration
	 */
	function __construct(array $conf = array())
	{
		global $db;

		if (!isset($_SESSION['user_agent'])) $_SESSION['user_agent'] = '';

		if (isset($conf['check_ip'])) $this->check_ip = $conf['check_ip'];
		if (isset($conf['check_useragent'])) $this->check_useragent = $conf['check_useragent'];

		if (isset($conf['sha1_key'])) $this->sha1_key = $conf['sha1_key'];
		if (isset($conf['allow_login'])) $this->allow_login = $conf['allow_login'];
		if (isset($conf['allow_registration'])) $this->allow_registration = $conf['allow_registration'];
		if (isset($conf['reserved_usercheck'])) $this->reserved_usercheck = $conf['reserved_usercheck'];
		if (isset($conf['userdata'])) $this->userdata = $conf['userdata'];
		if (isset($conf['mail_activate'])) $this->mail_activate = $conf['mail_activate'];

		if (isset($conf['minlen_username'])) $this->minlen_username = $conf['minlen_username'];
		if (isset($conf['minlen_password'])) $this->minlen_password = $conf['minlen_password'];

		if (isset($conf['smtp_host'])) $this->smtp_host = $conf['smtp_host'];
		if (isset($conf['smtp_username'])) $this->smtp_username = $conf['smtp_username'];
		if (isset($conf['smtp_password'])) $this->smtp_password = $conf['smtp_password'];
		if (isset($conf['mail_from'])) $this->mail_from = $conf['mail_from'];
		if (isset($conf['mail_from_name'])) $this->mail_from_name = $conf['mail_from_name'];

		if (isset($conf['mail_activate_msg'])) $this->mail_activate_msg = $conf['mail_activate_msg'];
		if (isset($conf['mail_password_msg'])) $this->mail_password_msg = $conf['mail_password_msg'];

		$this->ip = &$_SESSION['ip'];
		$this->user_agent = &$_SESSION['user_agent'];

		if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) {	//FIXME move ip check to auth_default
			$ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
			if (isBlocked(BLOCK_IP, $ip)) {
				die('You have been blocked from this site.');
			}
			$this->ip = $ip;
		}
		if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

		$this->handleAuthEvents();
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
	 * Logs in a user
	 *
	 * @param $username
	 * @param $password
	 */
	abstract function login($username, $password);

	/**
	 * Logs out the user
	 */
	abstract function logout();

	/**
	 * Displays built in login form
	 */
	abstract function showLoginForm();

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents()
	{
		global $config, $session;

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			$this->error = t('Client IP changed.');
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
		}

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$session->id) {
			if (!empty($_POST['login_usr']) && isset($_POST['login_pwd']) && $this->login($_POST['login_usr'], $_POST['login_pwd'])) {
				$session->startPage();
			}
		}

		//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
		if (isset($_GET['logout'])) {
			$this->logout();
			$session->loggedOutStartPage();
		}

		//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
		if (($this->allow_registration || !Users::cnt()) && !$session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2'])) {
			$preId = 0;
			if (!empty($_POST['preId']) && is_numeric($_POST['preId'])) $preId = $_POST['preId'];
			$check = $this->registerUser($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL, $preId);
			if (is_numeric($check)) {
				if ($this->mail_activate) {
					$this->sendActivationMail($check);
				} else {
					$this->login($_POST['register_usr'], $_POST['register_pwd']);
				}
			} else {
				$session->error = t('Registration failed').', '.$check;
			}
		}

		//Check if client user agent string changed
		if ($this->check_useragent && $this->user_agent != $_SERVER['HTTP_USER_AGENT']) {
			//FIXME this breaks when Firefox autoupdates & restarts
			//FIXME this occured once for a IE7 user while using embedded WMP11 + core_dev:
			//	"Client user agent string changed from "Windows-Media-Player/11.0.5721.5145" to "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)""
			//	also, this could be triggered if user is logged in & Firefox decides to auto-upgrade and restore previous tabs and sessions after restart

			$this->error = t('Client user agent string changed.');
			$this->log('Client user agent string changed from "'.$this->user_agent.'" to "'.$_SERVER['HTTP_USER_AGENT'].'"', LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
		}
	}

	/**
	 * FIXME move this to functions_email.php. remove smtp settings from Auth. use $config['email']
	 */
	function SmtpSend($dst_adr, $subj, $msg)
	{
		$mail = new PHPMailer();

		$mail->Mailer = 'smtp';
		$mail->Host = $this->smtp_host;
		$mail->Username = $this->smtp_username;
		$mail->Password = $this->smtp_password;

		$mail->CharSet  = 'utf-8';

		$mail->From = $this->mail_from;
		$mail->FromName = $this->mail_from_name;

		$mail->IsHTML(false); // send HTML mail?

		$mail->AddAddress($dst_adr);
		$mail->Subject = $subj;
		$mail->Body = $msg;

		if (!$mail->Send()) {
			$this->mail_error = true;
			return false;
		}
		return true;
	}

	/**
	 * Sends a account activation mail to specified user
	 *
	 * @param $_id user id
	 */
	function sendActivationMail($_id)
	{
		global $config;
		if (!is_numeric($_id)) return false;

		$email = loadUserdataEmail($_id);
		if (!$email) return false;

		$code = generateActivationCode(ACTIVATE_EMAIL, 1000000, 9999999);
		createActivation(ACTIVATE_EMAIL, $code, $_id);

		$subj = t('Account activation');

		$pattern = array('/__USERNAME__/', '/__IP__/', '/__CODE__/', '/__URL__/', '/__EXPIRETIME__/');
		$replacement = array(
			Users::getName($_id),
			$_SERVER['REMOTE_ADDR'],
			$code,
			$config['app']['full_url']."activate.php?id=".$_id."&code=".$code,
			shortTimePeriod($config['activate']['expire_time_email'])
		);
		$msg = preg_replace($pattern, $replacement, $this->mail_activate_msg);

		if (!$this->SmtpSend($email, $subj, $msg)) return false;

		$this->activation_sent = true;
		return true;
	}

	/**
	 * Verifies user activaction code
	 *
	 * @param $_id
	 * @param $_code
	 * @return true if success
	 */
	function verifyActivationMail($_id, $_code)
	{
		if (!is_numeric($_id) || !is_numeric($_code)) return false;

		if (!verifyActivation(ACTIVATE_EMAIL, $_code, $_id)) {
			echo t('Activation code is invalid or expired.');
			return false;
		}

		Users::activate($_id);

		removeActivation(ACTIVATE_EMAIL, $_code);

		echo t('Your account has been activated.').'<br/>';
		echo t('You can now proceed to').' <a href="login.php">'.t('log in').'</a>.';
		return true;
	}

	/**
	 * Looks up user supplied email address / alias and generates a mail for them if needed
	 *
	 * @param $email email address
	 */
	function handleForgotPassword($email)
	{
		global $config, $session;

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

	/**
	 * Reset user's password
	 *
	 * @param $_id user id
	 * @param $_code reset code
	 * @return true on success
	 */
	function resetPassword($_id, $_code)
	{
		global $session;
		if (!is_numeric($_id) || !is_numeric($_code)) return false;

		if (!verifyActivation(ACTIVATE_CHANGE_PWD, $_code, $_id)) {
			echo t('Activation code is invalid or expired.');
			return false;
		}

		echo '<h1>'.t('Set a new password').'</h1>';

		if (isset($_POST['reset_pwd1']) && isset($_POST['reset_pwd2'])) {
			$chk = Users::setPassword($_id, $_POST['reset_pwd1'], $_POST['reset_pwd2']);
			if ($chk) {
				echo t('Your password has been changed!');
				removeActivation(ACTIVATE_CHANGE_PWD, $_code);
				return true;
			}
		}

		echo t('Because we don\'t store the password in clear text it cannot be retrieved.').'<br/>';
		echo t('You will therefore need to set a new password for your account.').'<br/>';

		if ($session->error) {
			echo '<div class="critical">'.$session->error.'</div><br/>';
			$session->error = ''; //remove error message once it has been displayed
		}

		echo xhtmlForm();
		echo t('New password').': '.xhtmlPassword('reset_pwd1', '', 12).'<br/>';
		echo t('Repeat password').': '.xhtmlPassword('reset_pwd2', '', 12).'<br/>';
		echo xhtmlSubmit('Set password');
		echo xhtmlFormClose();
	}

}
?>
