<?
/**
 * $Id$
 *
 * Standard authentication module. Uses core_dev's own tblUsers
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('class.Auth_Base.php');
require_once('class.Users.php');

require_once('atom_moderation.php');	//for checking if username is reserved on user registration
require_once('functions_userdata.php');	//for showRequiredUserdataFields()
require_once('functions_locale.php'); //for translations

class Auth_Standard extends Auth_Base
{
	/**
	 * Register new user in the database
	 *
	 * \param $username user name
	 * \param $password1 password
	 * \param $password2 password (repeat)
	 * \param $_mode user mode
	 * \param $newUserId supply reserved user id. if not supplied, a new user id will be allocated
	 * \return the user ID of the newly created user
	 */
	function registerUser($username, $password1, $password2, $_mode = USERLEVEL_NORMAL, $newUserId = 0)
	{
		global $db, $config, $session;

		if (!is_numeric($_mode)) return false;

		if ($username != trim($username)) return t('Username contains invalid spaces');

		if (($db->escape($username) != $username) || ($db->escape($password1) != $password1)) {
			//if someone tries to enter ' or " etc as username/password letters
			//with this check, we dont need to encode the strings for use in sql query
			return t('Username or password contains invalid characters');
		}

		if (strlen($username) < 3) return t('Username must be at least 3 characters long');
		if (strlen($password1) < 4) return t('Password must be at least 4 characters long');
		if ($password1 != $password2) return t('The passwords doesnt match');

		if (!$session->isSuperAdmin) {
			if ($this->reserved_usercheck && isReservedUsername($username)) return t('Username is not allowed');

			//Checks if email was required, and if so if it was correctly entered
			if (!empty($config['auth']['userdata'])) {
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
			$newUserId = $db->insert($q);
		} else {
			$q = 'UPDATE tblUsers SET userName="'.$username.'",userMode='.$_mode.',timeCreated=NOW() WHERE userId='.$newUserId;
			$db->update($q);
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
		global $db;
		$q = 'INSERT INTO tblUsers SET userMode=0';
		return $db->insert($q);
	}

	/**
	 * Handles logins
	 *
	 * \param $username
	 * \param $password
	 * \return true on success
	 */
	function login($username, $password)
	{
		global $db, $session;

		$data = Users::validLogin($username, $password);

		if (!$data) {
			$session->error = t('Login failed');
			$session->log('Failed login attempt: username '.$username, LOGLEVEL_WARNING);
			return false;
		}

		if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
			if ($this->mail_activate && !Users::isActivated($data['userId'])) {
				$session->error = t('This account has not yet been activated.');
				return false;
			}

			if (!$this->allow_login) {
				$session->error = t('Logins currently not allowed.');
				return false;
			}
		}

		$session->startSession($data['userId'], $data['userName'], $data['userMode']);

		//Update last login time
		$db->update('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$session->id);
		$db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$session->id.', IP='.$session->ip.', userAgent="'.$db->escape($_SERVER['HTTP_USER_AGENT']).'"');

		return true;
	}

	/**
	 * Logs out the user
	 */
	function logout()
	{
		global $db, $session;

		$db->update('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$session->id);
		$session->endSession();
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
		if ($forgot_pwd && !$session->id) {
			if (isset($_POST['forgot_pwd'])) {
				$check = $this->handleForgotPassword($_POST['forgot_pwd']);
				if (!$check) {
					$session->error = t('The specified email address does not match any registered user.');
				}
				$tab = 'forgot_pwd';
			}
		}

		if (isset($_POST['register_usr'])) {
			$tab = 'register';
		}

		if ($session->error) {
			echo '<div class="critical">'.$session->error.'</div><br/>';
			$session->error = ''; //remove error message once it has been displayed
		}

		echo '<div id="login_form_layer"'.($tab!='login'?' style="display: none;"':'').'>';
		if (!$this->allow_login) {
			echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
		}
		echo '<form name="login_form" method="post" action="">';

		echo '<table cellpadding="2">';
		echo '<tr><td>'.t('Username').':</td><td><input name="login_usr" type="text"/> <img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/></td></tr>';
		echo '<tr><td>'.t('Password').':</td><td><input name="login_pwd" type="password"/> <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
		echo '</table>';
		echo '<br/>';
		echo '<input type="submit" class="button" value="'.t('Log in').'"/>';
		if (($this->allow_login && $this->allow_registration) || $allow_superadmin_reg) {
			echo '<input type="button" class="button" value="'.t('Register').'" onclick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_register_layer\');"/>';
			if ($forgot_pwd) {
				echo '<input type="button" class="button" value="Forgot password" onclick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
			}
		}
		echo '</form>';
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

					echo '<form method="post" action="">';
					echo '<table cellpadding="2">';
					echo '<tr>'.
									'<td>'.t('Username').':</td>'.
									'<td><input name="register_usr" type="text"'.(!empty($_POST['register_usr'])?' value="'.$_POST['register_usr'].'"':'').'/> '.
										'<img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/>'.
									'</td>'.
								'</tr>';
					echo '<tr><td>'.t('Password').':</td><td><input name="register_pwd" type="password"/> <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
					echo '<tr><td>'.t('Again').':</td><td><input name="register_pwd2" type="password"/> <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
					if ($this->userdata) {
						showRequiredUserdataFields();
					}
					echo '</table><br/>';

					if (!$allow_superadmin_reg) {
						echo '<input type="button" class="button" value="'.t('Log in').'" onclick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_form_layer\');"/>';
					}
					echo '<input type="submit" class="button" value="'.t('Register').'" style="font-weight: bold;"/>';
					if ($forgot_pwd) {
						echo '<input type="button" class="button" value="'.t('Forgot password').'" onclick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
					}
					echo '</form>';
				echo '</div>';
			}

			if ($forgot_pwd) {
				echo '<div id="login_forgot_pwd_layer"'.($tab!='forgot_pwd'?' style="display: none;"':'').'>';

				if ($this->resetpwd_sent) {
					echo t('A email has been sent to your mail address with instructions how to reclaim your account.');
				} else {
					echo '<form method="post" action="">';
					echo 'Enter the e-mail address used when registering your account.<br/><br/>';
					echo 'You will recieve an e-mail with a link to follow,<br/>';
					echo 'where you can set a new password.<br/><br/>';
					echo '<table cellpadding="2">';
					echo '<tr><td>'.getUserdataFieldName($forgot_pwd).':</td><td><input type="text" name="forgot_pwd" size="26"/> <img src="'.$config['core']['web_root'].'gfx/icon_mail.png" alt="E-Mail"/></td></tr>';
					echo '</table><br/>';

					echo '<input type="button" class="button" value="'.t('Log in').'" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_form_layer\');"/>';
					echo '<input type="button" class="button" value="'.t('Register').'" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_register_layer\');"/>';
					echo '<input type="submit" class="button" value="'.t('Forgot password').'" style="font-weight: bold;"/>';
					echo '</form>';
				}
				echo '</div>';
			}
		}

		echo '</div>';
	}

	/**
	 * Displays a account registration form
	 *
	 * \param $preId userId previously created to use, instead of creating a new id (optional)
	 * \param $act_code activation code supplied to finish account creation
	 * \return true if registration was successful & activation mail was sent out
	 */
	function showRegisterForm($preId = 0, $act_code = 0)
	{
		global $config, $session;
		if (!is_numeric($preId) || !is_numeric($act_code)) return false;

		if ($this->mail_error) {
			echo '<div class="critical">Ett fel uppstod när aktiveringsmail skulle skickas ut!</div><br/>';
			return false;
		}

		if ($session->error) {
			echo '<div class="critical">'.$session->error.'</div><br/>';
			$session->error = ''; //remove error message once it has been displayed
		}

		if ($this->activation_sent) {
			echo 'Ett email med din aktiveringskod har skickats.<br/>';
			echo 'Följ länken i mailet för att slutföra din registrering.<br/>';
			return true;
		}

		echo '<form method="post" action="">';
		if ($preId) {
			echo '<input type="hidden" name="preId" value="'.$preId.'"/>';
		}
		echo '<table cellpadding="2">';
		echo '<tr>'.
				'<td>Användarnamn:</td>'.
				'<td><input name="register_usr" type="text"'.(!empty($_POST['register_usr'])?' value="'.$_POST['register_usr'].'"':'').'/> '.
					'<img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/>'.
				'</td>'.
				'</tr>';
		echo '<tr><td>'.t('Password').':</td><td><input name="register_pwd" type="password"/> <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
		echo '<tr><td>'.t('Repeat password').':</td><td><input name="register_pwd2" type="password"/> <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
		if ($this->userdata) {
			showRequiredUserdataFields();
		}
		echo '</table><br/>';

		if ($act_code) {
			echo '<input type="hidden" name="c" value="'.$act_code.'"/>';
		}
		echo '<input type="submit" class="button" value="'.t('Register').'"/>';
		echo '</form>';
		return false;
	}

	function changePasswordForm()
	{
		global $session;
		if (!$session->id) return false;

		$check = false;

		if (!empty($_POST['oldpwd']) && isset($_POST['pwd1']) && isset($_POST['pwd2'])) {
			if (Users::validLogin($session->username, $_POST['oldpwd'])) {
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
			echo '<form method="post" action="">';
			echo '<table cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td>'.t('Current password').':</td><td><input type="password" name="oldpwd"/></td></tr>';
			echo '<tr><td>'.t('New password').':</td><td><input type="password" name="pwd1"/></td></tr>';
			echo '<tr><td>'.t('Repeat password').':</td><td><input type="password" name="pwd2"/></td></tr>';
			echo '<tr><td colspan="2"><input type="submit" class="button" value="'.t('Change password').'"/></td></tr>';
			echo '</table>';
			echo '</form>';
		} else {
			echo t('Your password has been changed successfully!');
		}
	}

}	
?>
