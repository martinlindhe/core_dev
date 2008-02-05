<?
/**
 * $Id$
 *
 * Skeleton for authentication modules
 *
 * \todo libapache2-mod-auth-openid module
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_activate.php');		//for activation
require_once('ext/class.phpmailer.php');	//for outgoing mail. FIXME look for bsd-compatible mailer

abstract class Auth_Base
{
	public $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	///< used to further encode sha1 passwords, to make rainbow table attacks harder

	public $allow_login = true;						///< set to false to only let superadmins log in to the site
	public $allow_registration = true;		///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
	public $reserved_usercheck = true;		///< check if username is listed as reserved username, requires tblStopwords
	public $userdata = true; 							///< shall we use tblUserdata for required userdata fields?
	public $mail_activate = false;				///< does account registration require email activation?

	public $activation_sent = false;			///< internal. true if mail activation has been sent

	function __construct(array $auth_conf = array())
	{
		global $db;

		if (isset($auth_conf['sha1_key'])) $this->sha1_key = $auth_conf['sha1_key'];
		if (isset($auth_conf['allow_login'])) $this->allow_login = $auth_conf['allow_login'];
		if (isset($auth_conf['allow_registration'])) $this->allow_registration = $auth_conf['allow_registration'];
		if (isset($auth_conf['reserved_usercheck'])) $this->reserved_usercheck = $auth_conf['reserved_usercheck'];
		if (isset($auth_conf['userdata'])) $this->userdata = $auth_conf['userdata'];
		if (isset($auth_conf['mail_activate'])) $this->mail_activate = $auth_conf['mail_activate'];

		$this->handleAuthEvents();
	}

	abstract function registerUser($username, $password1, $password2, $userMode = 0);

	//FIXME abstract function unregisterUser()

	abstract function login($username, $password);

	abstract function logout();

	abstract function showLoginForm();

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents()
	{
		global $config, $session;

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$session->id) {
			if (!empty($_POST['login_usr']) && isset($_POST['login_pwd']) && $this->login($_POST['login_usr'], $_POST['login_pwd'])) {
				$session->startPage();
			}
		}

		//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
		if (isset($_GET['logout'])) {
			$this->logout();
			$session->startPage();
		}

		//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
		if ($this->allow_registration && !$session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2'])) {
			$check = $this->registerUser($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']);
			if (is_numeric($check)) {
				if ($this->mail_activate) {
					$this->sendActivationMail($check);
				} else {
					$this->login($_POST['register_usr'], $_POST['register_pwd']);
				}
			} else {
				$session->error = 'Registration failed, '.$check;
			}
		}
	}

	/**
	 * Sends a account activation mail to specified user
	 */
	function sendActivationMail($_id)
	{
		global $config;
		if (!is_numeric($_id)) return false;

		$adr = loadUserdataEmail($_id);
		if (!$adr) return false;

		$code = generateActivationCode(1000000, 9999999);
		$act_id = createActivation(ACTIVATE_EMAIL, $code, $_id);

		$mail = new PHPMailer();

		$mail->Mailer = 'smtp';
		$mail->Host = 'mail.unicorn.tv';	//FIXME gör smtp server konfigurerbar
		//$mail->Username = 'usr';
		//$mail->Password = 'pwd';

		$mail->CharSet  = 'utf-8';

		$mail->From     = 'noreply@example.com';
		$mail->FromName = 'core_dev';

		$mail->IsHTML(false);   // send HTML mail

		$mail->Subject  = "core_dev activation";

		$msg =
			"Hello. Someone (probably you) registered an account from IP ".$_SERVER['REMOTE_ADDR']."\n".
			"\n".
			"Username: ".Users::getName($_id)."\n".
			"Activation code: ".$code."\n".
			"\n".
			"Follow this link to activate your account:\n".
			"http://priv.localhost/core_dev/sample/activate.php?id=".$_id."&code=".$code."\n".
			"\n".
			"The activation code will expire in ".shortTimePeriod($config['activate']['expire_time_email'])."\n";

		$mail->AddAddress($adr);
		$mail->Body = $msg;
		if (!$mail->Send()) return false;

		$this->activation_sent = true;
	}

	function verifyActivationMail($_id, $_code)
	{
		if (!is_numeric($_id) || !is_numeric($_code)) return false;

		if (!verifyActivation(ACTIVATE_EMAIL, $_code, $_id)) {
			echo 'Activation code is invalid or expired.';
			return false;
		}

		Users::activate($_id);

		removeActivation(ACTIVATE_EMAIL, $_code);

		echo 'Your account has been activated<br/>';
		echo 'You can now proceed to <a href="login.php">log in</a>.';
		return true;
	}

}
?>