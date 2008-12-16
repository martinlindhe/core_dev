<?php
/**
 * $Id$
 *
 * Default authentication class. Uses core_dev's own tblUsers
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('auth_base.php');
require_once('class.Users.php');

require_once('atom_moderation.php');	//for checking if username is reserved on user registration
require_once('atom_events.php');		//for event logging
require_once('functions_userdata.php');	//for showRequiredUserdataFields()

require_once('atom_activation.php');		//for activation
require_once('functions_ip.php');			//for IPv4_to_GeoIP()
require_once('atom_blocks.php');			//for isBlocked()

class auth_default extends auth_base
{
	var $driver = 'default';
	var $db = false;	///< points to $db driver to use
	var $error = '';	///< contains last error message, if any

	private $check_ip = true;				///< client will be logged out if client ip is changed during the session
	private $check_useragent = false;		///< keeps track if the client user agent string changes during the session
	private $ip = false;					///< IP of user

	function __construct($db = false, $conf = array())
	{
		$this->db = &$db;

		if (isset($conf['check_ip'])) $this->check_ip = $conf['check_ip'];
		if (isset($conf['check_useragent'])) $this->check_useragent = $conf['check_useragent'];

		if (!isset($_SESSION['user_agent'])) $_SESSION['user_agent'] = '';

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
	}

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents($session, $user)
	{
		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$session->id) {
			if (!empty($_POST['login_usr']) && isset($_POST['login_pwd']) && $user->login($_POST['login_usr'], $_POST['login_pwd'])) {
				$session->startPage();
			}
		}

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			$this->error = t('Client IP changed.');
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
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
	 * Register new user in the database
	 *
	 * @param $username user name
	 * @param $password1 password
	 * @param $password2 password (repeat)
	 * @param $_mode user mode
	 * @param $newUserId supply reserved user id. if not supplied, a new user id will be allocated
	 * @return the user ID of the newly created user
	 */
	function registerUser($username, $password1, $password2, $_mode = USERLEVEL_NORMAL, $newUserId = 0)
	{
		global $config, $session;
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
	 * Creates a tblUsers entry without username or password
	 */
	function reserveUser()
	{
		$q = 'INSERT INTO tblUsers SET userMode=0';
		return $this->db->insert($q);
	}

	/**
	 * Shows a login form with tabs for Register & Forgot password functions
	 *
	 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
	 */
	function showLoginForm()
	{
		global $config, $session;
		echo '<div class="login_box">';

		$tab = 'login';	//default tab show login form

		$allow_superadmin_reg = false;
		if (!Users::cnt()) {
			$allow_superadmin_reg = true;
			$tab = 'register';
		}

		$forgot_pwd = false;
		if ($this->userdata) {
			$forgot_pwd = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
		}

		//Check for "forgot password" request, POST to any page with 'forgot_pwd' set
		if ($forgot_pwd && !$session->id && isset($_POST['forgot_pwd'])) {
			$check = $this->handleForgotPassword($_POST['forgot_pwd']);
			if (!$check) {
				$session->error = t('The specified email address does not match any registered user.');
			}
			$tab = 'forgot_pwd';
		}

		if (isset($_POST['register_usr'])) {
			$tab = 'register';
		}

		if ($this->error) {
			echo '<div class="critical">'.$this->error.'</div><br/>';
			$this->error = ''; //remove error message once it has been displayed
		}

		echo '<div id="login_form_layer"'.($tab!='login'?' style="display: none;"':'').'>';
		if (!$this->allow_login) {
			echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
		}
		echo xhtmlForm('login_form');

		echo '<table cellpadding="2">';
		echo '<tr><td>'.t('Username').':</td><td>'.xhtmlInput('login_usr').' <img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/></td></tr>';
		echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('login_pwd').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
		echo '</table>';
		echo '<br/>';
		echo xhtmlSubmit('Log in', 'button', 'font-weight: bold');
		if (($this->allow_login && $this->allow_registration) || $allow_superadmin_reg) {
			echo xhtmlButton('Register', "hide_element_by_name('login_form_layer'); show_element_by_name('login_register_layer')");
		}
		if ($forgot_pwd) {
			echo xhtmlButton('Forgot password', "hide_element_by_name('login_form_layer'); show_element_by_name('login_forgot_pwd_layer')");
		}
		echo xhtmlFormClose();
		echo '</div>';

		if (($this->allow_login && $this->allow_registration) || $allow_superadmin_reg) {
			echo '<div id="login_register_layer"'.($tab!='register'?' style="display: none;"':'').'>';

				if ($this->activation_sent) {
					echo t('An email with your activation code has been sent.').'<br/>';
					echo t('Follow the link in the mail to complete your registration.').'<br/>';
					/*	//FIXME implement this:
					echo 'You can also enter activation code here to finish:<br/>';
					echo '<form method="post" action="">';
					echo '<input type="text" size="10"/>';
					echo '<input type="submit" class="button" value="Complete registration"/>';
					echo '</form>';
					*/
				} else {

					echo '<b>'.t('Register new account').'</b><br/><br/>';
					if ($allow_superadmin_reg) {
						echo '<div class="critical">'.t('The account you create now will be the super administrator account.').'</div><br/>';
					}

					echo xhtmlForm();
					echo '<table cellpadding="2">';
					echo '<tr>'.
							'<td>'.t('Username').':</td>'.
							'<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
								'<img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/>'.
							'</td>'.
						'</tr>';
					echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('register_pwd').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
					echo '<tr><td>'.t('Again').':</td><td>'.xhtmlPassword('register_pwd2').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
					if ($this->userdata) {
						showRequiredUserdataFields();
					}
					echo '</table><br/>';

					if (!$allow_superadmin_reg) {
						echo xhtmlButton('Log in', "hide_element_by_name('login_register_layer'); show_element_by_name('login_form_layer')");
					}
					echo xhtmlSubmit('Register', 'button', 'font-weight: bold');
					if ($forgot_pwd) {
						echo xhtmlButton('Forgot password', "hide_element_by_name('login_register_layer'); show_element_by_name('login_forgot_pwd_layer')");
					}
					echo xhtmlFormClose();
				echo '</div>';
			}
		}

		if ($forgot_pwd) {
			echo '<div id="login_forgot_pwd_layer"'.($tab!='forgot_pwd'?' style="display: none;"':'').'>';

			if ($this->resetpwd_sent) {
				echo t('A email has been sent to your mail address with instructions how to reclaim your account.');
			} else {
				echo xhtmlForm();
				echo 'Enter the e-mail address used when registering your account.<br/><br/>';
				echo 'You will recieve an e-mail with a link to follow,<br/>';
				echo 'where you can set a new password.<br/><br/>';
				echo '<table cellpadding="2">';
				echo '<tr><td>'.getUserdataFieldName($forgot_pwd).':</td><td>'.xhtmlInput('forgot_pwd', '', 26).' <img src="'.$config['core']['web_root'].'gfx/icon_mail.png" alt="'.t('E-mail').'"/></td></tr>';
				echo '</table><br/>';

				echo xhtmlButton('Log in', "hide_element_by_name('login_forgot_pwd_layer'); show_element_by_name('login_form_layer')");
				echo xhtmlButton('Register', "hide_element_by_name('login_forgot_pwd_layer'); show_element_by_name('login_register_layer')");
				echo xhtmlSubmit('Forgot password', 'button', 'font-weight: bold');
				echo xhtmlFormClose();
			}
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Displays a account registration form
	 *
	 * @param $preId userId previously created to use, instead of creating a new id (optional)
	 * @param $act_code activation code supplied to finish account creation
	 * @return true if registration was successful & activation mail was sent out
	 */
	function showRegisterForm($preId = 0, $act_code = 0)
	{
		global $config, $session;
		if (!is_numeric($preId) || !is_numeric($act_code)) return false;

		if ($this->mail_error) {
			echo '<div class="critical">'.t('An error occured sending activation mail!').'</div><br/>';
			return false;
		}

		if ($session->error) {
			echo '<div class="critical">'.$session->error.'</div><br/>';
			$session->error = ''; //remove error message once it has been displayed
		}

		if ($this->activation_sent) {
			echo t('An email with your activation code has been sent.').'<br/>';
			echo t('Follow the link in the mail to finish your registration.').'<br/>';
			return true;
		}

		echo xhtmlForm();
		if ($preId) {
			echo '<input type="hidden" name="preId" value="'.$preId.'"/>';
		}
		echo '<table cellpadding="2">';
		echo '<tr>'.
				'<td>'.t('Username').':</td>'.
				'<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
					'<img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/>'.
				'</td>'.
				'</tr>';
		echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('register_pwd').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
		echo '<tr><td>'.t('Repeat password').':</td><td>'.xhtmlPassword('register_pwd2').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
		if ($this->userdata) {
			showRequiredUserdataFields();
		}
		echo '</table><br/>';

		if ($act_code) {
			echo xhtmlHidden('c', $act_code);
		}
		echo xhtmlSubmit('Register');
		echo xhtmlFormClose();
		return false;
	}

	/**
	 * Helper to change the user's current password.
	 */
	function changePasswordForm()
	{
		global $session;
		if (!$session->id) return false;

		$check = false;

		if (!empty($_POST['oldpwd']) && isset($_POST['pwd1']) && isset($_POST['pwd2'])) {
			if ($this->validLogin($session->username, $_POST['oldpwd'])) {
				$check = Users::setPassword($session->id, $_POST['pwd1'], $_POST['pwd2']);
			} else {
				$session->error = t('Current password is incorrect');
			}
		}

		if ($session->error) {
			echo '<div class="critical">'.$session->error.'</div><br/>';
			$session->error = '';
		}

		if (!$check) {
			echo xhtmlForm();
			echo '<table cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td>'.t('Current password').':</td><td>'.xhtmlPassword('oldpwd').'</td></tr>';
			echo '<tr><td>'.t('New password').':</td><td>'.xhtmlPassword('pwd1').'</td></tr>';
			echo '<tr><td>'.t('Repeat password').':</td><td>'.xhtmlPassword('pwd2').'</td></tr>';
			echo '<tr><td colspan="2">'.xhtmlSubmit('Change password').'</td></tr>';
			echo '</table>';
			echo xhtmlFormClose();
		} else {
			echo t('Your password has been changed successfully!');
		}
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
