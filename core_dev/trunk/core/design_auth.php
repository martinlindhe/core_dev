<?php
/**
 * $Id$
 *
 * Default functions for auth drivers
 */

//TODO: implement ajax callbacks for showRegisterForm() to warn about taken username, taken email address
//TODO: js validation functions in showRegisterForm() to warn about invalid email format and incorrect repeated password
//TODO: update changePasswordForm()
//TODO: update resetPassword()

require_once('output_xhtml.php');

/**
 * Shows a login form with tabs for Register & Forgot password functions
 *
 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
 */
function showLoginForm()
{
	global $h;

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

	//Check for "forgot password" request, POST to any page with 'forgot_pwd' set
	if ($forgot_pwd && !$h->session->id && isset($_POST['forgot_pwd'])) {
		$check = $h->auth->handleForgotPassword($_POST['forgot_pwd']);
		if (!$check) {
			$h->error = t('The specified email address does not match any registered user.');
		}
		$tab = 'forgot_pwd';
	}

	if (isset($_POST['register_usr'])) {
		$tab = 'register';
	}

	$h->showError();

	echo '<div id="login_form_layer"'.($tab!='login'?' style="display: none;"':'').'>';
	if (!$h->auth->allow_login) {
		echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
	}
	echo xhtmlForm('login_form');

	echo '<table cellpadding="2">';
	echo '<tr>'.
			'<td>'.t('Username').':</td>'.
			'<td>'.xhtmlInput('login_usr').' '.
				xhtmlImage(coredev_webroot().'gfx/icon_user.png', t('Username')).
			'</td>'.
		'</tr>';
	echo '<tr>'.
			'<td>'.t('Password').':</td>'.
			'<td>'.xhtmlPassword('login_pwd').' '.
				xhtmlImage(coredev_webroot().'gfx/icon_keys.png', t('Password')).
			'</td>'.
		'</tr>';
	echo '</table>';
	echo '<br/>';
	echo xhtmlSubmit('Log in', 'button', 'font-weight: bold');
	if (($h->auth->allow_login && $h->auth->allow_registration) || $allow_superadmin_reg) {
		echo xhtmlButton('Register', "hide_element('login_form_layer'); show_element('login_register_layer')");
	}
	if ($forgot_pwd) {
		echo xhtmlButton('Forgot password', "hide_element('login_form_layer'); show_element('login_forgot_pwd_layer')");
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
							xhtmlImage(coredev_webroot().'gfx/icon_user.png', t('Username')).
						'</td>'.
					'</tr>';
				echo '<tr><td>'.t('Password').':</td>'.
						'<td>'.xhtmlPassword('register_pwd').' '.
							xhtmlImage(coredev_webroot().'gfx/icon_keys.png', t('Password')).
						'</td>'.
					'</tr>';
				echo '<tr><td>'.t('Again').':</td>'.
						'<td>'.xhtmlPassword('register_pwd2').' '.
							xhtmlImage(coredev_webroot().'gfx/icon_keys.png', t('Repeat password')).
						'</td>'.
					'</tr>';
				if ($h->user->userdata) {
					showRequiredUserdataFields();
				}
				echo '</table><br/>';

				if (!$allow_superadmin_reg) {
					echo xhtmlButton('Log in', "hide_element('login_register_layer'); show_element('login_form_layer')");
				}
				echo xhtmlSubmit('Register', 'button', 'font-weight: bold');
				if ($forgot_pwd) {
					echo xhtmlButton('Forgot password', "hide_element('login_register_layer'); show_element('login_forgot_pwd_layer')");
				}
				echo xhtmlFormClose();
			echo '</div>';
		}
	}

	if ($forgot_pwd) {
		echo '<div id="login_forgot_pwd_layer"'.($tab!='forgot_pwd'?' style="display: none;"':'').'>';
/*
 *  * XXX FIXME how to read resetpwd_sent
		if ($this->resetpwd_sent) {
			echo t('A email has been sent to your mail address with instructions how to reclaim your account.');
		} else */{
			echo xhtmlForm();
			echo 'Enter the e-mail address used when registering your account.<br/><br/>';
			echo 'You will recieve an e-mail with a link to follow,<br/>';
			echo 'where you can set a new password.<br/><br/>';
			echo '<table cellpadding="2">';
			echo '<tr><td>'.getUserdataFieldName($forgot_pwd).':</td><td>'.xhtmlInput('forgot_pwd', '', 26).' <img src="'.coredev_webroot().'gfx/icon_mail.png" alt="'.t('E-mail').'"/></td></tr>';
			echo '</table><br/>';

			echo xhtmlButton('Log in', "hide_element('login_forgot_pwd_layer'); show_element('login_form_layer')");
			echo xhtmlButton('Register', "hide_element('login_forgot_pwd_layer'); show_element('login_register_layer')");
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
	global $h;
	if (!is_numeric($preId) || !is_numeric($act_code)) return false;

	if ($h->auth->mail_error) {
		echo '<div class="critical">'.t('An error occured sending activation mail!').'</div><br/>';
		return false;
	}

	$h->showError();

	if ($h->auth->activation_sent) {
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
				'<img src="'.coredev_webroot().'gfx/icon_user.png" alt="'.t('Username').'"/>'.
			'</td>'.
			'</tr>';
	echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('register_pwd').' <img src="'.coredev_webroot().'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
	echo '<tr><td>'.t('Repeat password').':</td><td>'.xhtmlPassword('register_pwd2').' <img src="'.coredev_webroot().'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
	if ($h->user->userdata) {
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
		if ($this->validLogin($h->session->username, $_POST['oldpwd'])) {
			$check = Users::setPassword($h->session->id, $_POST['pwd1'], $_POST['pwd2']);
		} else {
			$session->error = t('Current password is incorrect');
		}
	}

	$h->showError();

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
	global $h;
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

	$h->showError();

	echo xhtmlForm();
	echo t('New password').': '.xhtmlPassword('reset_pwd1', '', 12).'<br/>';
	echo t('Repeat password').': '.xhtmlPassword('reset_pwd2', '', 12).'<br/>';
	echo xhtmlSubmit('Set password');
	echo xhtmlFormClose();
}

?>
