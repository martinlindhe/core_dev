<?php
/**
 * $Id$
 *
 * Default functions for auth drivers
 */

require_once('output_xhtml.php');

/**
 * Shows a login form with tabs for Register & Forgot password functions
 *
 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
 */
function showLoginForm()
{
	global $config, $h;

	if (!$h->auth) return false;

	echo '<div class="login_box">';

	$tab = 'login';	//default tab show login form

	$allow_superadmin_reg = false;
	if (!Users::cnt()) {
		$allow_superadmin_reg = true;
		$tab = 'register';
	}

	$forgot_pwd = false;
	if ($h->user->userdata) {
		$forgot_pwd = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
	}

	$error = '';

	//Check for "forgot password" request, POST to any page with 'forgot_pwd' set
	if ($forgot_pwd && !$session->id && isset($_POST['forgot_pwd'])) {
		$check = $h->auth->handleForgotPassword($_POST['forgot_pwd']);
		if (!$check) {
			$error = t('The specified email address does not match any registered user.');
		}
		$tab = 'forgot_pwd';
	}

	if (isset($_POST['register_usr'])) {
		$tab = 'register';
	}

	if ($error) {
		echo '<div class="critical">'.$error.'</div><br/>';
	}

	echo '<div id="login_form_layer"'.($tab!='login'?' style="display: none;"':'').'>';
	if (!$h->auth->allow_login) {
		echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
	}
	echo xhtmlForm('login_form');

	echo '<table cellpadding="2">';
	echo '<tr><td>'.t('Username').':</td><td>'.xhtmlInput('login_usr').' <img src="'.$config['core']['web_root'].'gfx/icon_user.png" alt="'.t('Username').'"/></td></tr>';
	echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('login_pwd').' <img src="'.$config['core']['web_root'].'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
	echo '</table>';
	echo '<br/>';
	echo xhtmlSubmit('Log in', 'button', 'font-weight: bold');
	if (($h->auth->allow_login && $h->auth->allow_registration) || $allow_superadmin_reg) {
		echo xhtmlButton('Register', "hide_element_by_name('login_form_layer'); show_element_by_name('login_register_layer')");
	}
	if ($forgot_pwd) {
		echo xhtmlButton('Forgot password', "hide_element_by_name('login_form_layer'); show_element_by_name('login_forgot_pwd_layer')");
	}
	echo xhtmlFormClose();
	echo '</div>';

	if (($h->auth->allow_login && $h->auth->allow_registration) || $allow_superadmin_reg) {
		echo '<div id="login_register_layer"'.($tab!='register'?' style="display: none;"':'').'>';

			if ($h->auth->activation_sent) {
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
				if ($h->user->userdata) {
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
	global $config;
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
	global $h;
	if (!$h->session->id) return false;

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
 * Reset user's password
 *
 * @param $_id user id
 * @param $_code reset code
 * @return true on success
 */
function resetPassword($_id, $_code)
{
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

?>
