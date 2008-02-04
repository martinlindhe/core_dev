<?
/**
 * $Id$
 *
 * Standard authentication module. Uses tblUsers
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('class.Auth_Base.php');

class Auth_Standard extends Auth_Base
{



/*
		if (!$this->id && !empty($_POST['register_usr']) && !empty($_POST['register_pwd']) && !empty($_POST['register_pwd2'])) {
			$check = $this->registerUser($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']);
			if (!is_numeric($check)) {
				echo 'Registration failed: '.$check;
				die;
			}
			$this->logIn($_POST['register_usr'], $_POST['register_pwd']);
		}

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$this->id) {
			if (!empty($_POST['login_usr']) && !empty($_POST['login_pwd']) && $this->logIn($_POST['login_usr'], $_POST['login_pwd'])) {
				header('Location: '.$config['web_root'].$this->start_page);
				die;
			}
		}

		//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
		if (isset($_GET['logout'])) {
			$this->logOut();
			header('Location: '.$config['web_root'].$this->start_page);
			die;
		}
*/






	/**
	 * Register new user in the database
	 * \param $username user name
	 * \param $password1 password
	 * \param $password2 password (repeat)
	 * \param $userMode user mode
	 * \return the user ID of the newly created user
	 */
	function registerUser($username, $password1, $password2, $userMode = 0)
	{
		global $db, $config;
		if (!is_numeric($userMode)) return false;

		if ($username != trim($username)) return 'Username contains invalid spaces';

		if (($db->escape($username) != $username) || ($db->escape($password1) != $password1)) {
			//if someone tries to enter ' or " etc as username/password letters
			//with this check, we dont need to encode the strings for use in sql query
			return 'Username or password contains invalid characters';
		}

		if (strlen($username) < 3) return 'Username must be at least 3 characters long';
		if (strlen($password1) < 4) return 'Password must be at least 4 characters long';

		if ($password1 != $password2) return 'The passwords doesnt match';

		if ($this->reserved_usercheck && isReservedUsername($username)) return 'Username is not allowed';

		if (getUsersCnt()) {
			$q = 'SELECT userId FROM tblUsers WHERE userName="'.$username.'"';
			$checkId = $db->getOneItem($q);
			if ($checkId) return 'Username already exists';
		} else {
			//No users exists, give this user superadmin status
			$userMode = 2;
		}

		$q = 'INSERT INTO tblUsers SET userName="'.$username.'",userPass="'.sha1( sha1($this->sha1_key).sha1($password1) ).'",userMode='.$userMode.',timeCreated=NOW()';
		$newUserId = $db->insert($q);

		$this->log('User <b>'.$username.'</b> created');

		//Stores the additional data from the userdata fields that's required at registration
		if ($this->userdata) {
			handleRequiredUserdataFields($newUserId);
		}

		return $newUserId;
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
		global $db;

		$enc_username = $db->escape($username);
		$enc_password = sha1( sha1($this->sha1_key).sha1($password) );

		$q = 'SELECT * FROM tblUsers WHERE userName="'.$enc_username.'" AND userPass="'.$enc_password.'"';
		$data = $db->getOneRow($q);
		if (!$data) {
			$this->error = 'Login failed';
			$this->log('Failed login attempt: username '.$enc_username, LOGLEVEL_WARNING);
			return false;
		}

		if ($data['userMode'] != 2 && !$this->allow_login) {
			$this->error = 'Logins currently not allowed.';
			return false;
		}
		
		$this->error = '';
		$this->username = $data['userName'];
		$this->id = $data['userId'];
		$this->mode = $data['userMode'];		//0=normal user. 1=admin, 2=super admin
		$this->lastActive = time();

		if ($this->mode >= 1) $this->isAdmin = 1;
		if ($this->mode >= 2) $this->isSuperAdmin = 1;
		
		//Update last login time
		$db->query('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$this->id);
		$db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$this->id.', IP='.$this->ip.', userAgent="'.$db->escape($_SERVER['HTTP_USER_AGENT']).'"');

		/* Read in current users settings */
		if ($this->allow_themes) {
			$this->theme = loadUserdataSetting($this->id, 'Theme', $this->default_theme);
		}

		$this->log('User logged in', LOGLEVEL_NOTICE);
		return true;
	}

	/**
	 * Logs out the user
	 */
	function logout()
	{
		global $db;

		$this->log('User logged out', LOGLEVEL_NOTICE);
		$db->query('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$this->id);

		$this->started = 0;
		$this->username = '';
		$this->id = 0;
		$this->ip = 0;
		$this->user_agent = '';
		$this->mode = 0;
		$this->isAdmin = 0;
		$this->isSuperAdmin = 0;
		$this->theme = $this->default_theme;
	}

	/**
	 * Shows a login form with tabs for Register & Forgot password functions
	 *
	 * the handling of the result variables are called by $session->handleSessionActions() from config.php 
	 */
	function showLoginForm()
	{
		global $config;
		echo '<div class="login_box">';

		$allow_superadmin_reg = false;
		if (!getUsersCnt()) {
			echo 'No users registered!';
			$allow_superadmin_reg = true;
		}

		$forgot_pwd = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);

		//Check for "forgot password" request, POST to any page with 'forgot_pwd' set
		if ($forgot_pwd && !$this->id) {
			if (!empty($_POST['forgot_pwd'])) {
				echo $_POST['forgot_pwd'];
			}
		}

		//FIXME: show appropriate tab on page reload


		echo '<div id="login_form_layer">';
		if (!$this->allow_login) {
			echo '<div class="critical">Logins are currently not allowed.<br/>Please try again later.</div>';
		}
		echo '<form name="login_form" method="post" action="">';
		if ($this->error) {
			echo '<div class="critical">'.$this->error.'</div>';
			$this->error = ''; //remove error message once it has been displayed
		}

		echo '<table cellpadding="2">';
		echo '<tr><td>Username:</td><td><input name="login_usr" type="text"/> <img src="'.$config['core_web_root'].'gfx/icon_user.png" alt="Username"/></td></tr>';
		echo '<tr><td>Password:</td><td><input name="login_pwd" type="password"/> <img src="'.$config['core_web_root'].'gfx/icon_keys.png" alt="Password"/></td></tr>';
		echo '</table>';
		echo '<br/>';
		echo '<input type="submit" class="button" value="Log in"/>';
		if (($this->allow_login && $this->allow_registration) || $allow_superadmin_reg) {
			echo '<input type="button" class="button" value="Register" onclick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_register_layer\');"/>';
			if ($forgot_pwd) {
				echo '<input type="button" class="button" value="Forgot password" onclick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
			}
		}
		echo '</form>';
		echo '</div>';

		if (($this->allow_login && $this->allow_registration) || $allow_superadmin_reg) {
			echo '<div id="login_register_layer" style="display: none;">';
				echo '<b>Register new account</b><br/><br/>';
				if ($allow_superadmin_reg) {
					echo '<div class="critical">The account you create now will be the super administrator account.</div>';
				}

				echo '<form method="post" action="">';
				echo '<table cellpadding="2">';
				echo '<tr><td>Username:</td><td><input name="register_usr" type="text"/> <img src="'.$config['core_web_root'].'gfx/icon_user.png" alt="Username"/></td></tr>';
				echo '<tr><td>Password:</td><td><input name="register_pwd" type="password"/> <img src="'.$config['core_web_root'].'gfx/icon_keys.png" alt="Password"/></td></tr>';
				echo '<tr><td>Again:</td><td><input name="register_pwd2" type="password"/> <img src="'.$config['core_web_root'].'gfx/icon_keys.png" alt="Repeat password"/></td></tr>';
				if ($this->userdata) {
					showRequiredUserdataFields();
				}
				echo '</table><br/>';

				echo '<input type="button" class="button" value="Log in" onclick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_form_layer\');"/>';
				echo '<input type="submit" class="button" value="Register" style="font-weight: bold;"/>';
				if ($forgot_pwd) {
					echo '<input type="button" class="button" value="Forgot password" onclick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
				}
				echo '</form>';
			echo '</div>';

			if ($forgot_pwd) {
				echo '<div id="login_forgot_pwd_layer" style="display: none;">';
					echo '<form method="post" action="">';
					echo 'Enter the e-mail address used when registering your account.<br/><br/>';
					echo 'You will recieve an e-mail with a link to follow,<br/>';
					echo 'where you can set a new password.<br/><br/>';
					echo '<table cellpadding="2">';
					echo '<tr><td>'.getUserdataFieldName($forgot_pwd).':</td><td><input type="text" name="forgot_pwd" size="26"/> <img src="'.$config['core_web_root'].'gfx/icon_mail.png" alt="E-Mail"/></td></tr>';
					echo '</table><br/>';

					echo '<input type="button" class="button" value="Log in" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_form_layer\');"/>';
					echo '<input type="button" class="button" value="Register" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_register_layer\');"/>';
					echo '<input type="submit" class="button" value="Forgot password" style="font-weight: bold;"/>';
					echo '</form>';
				echo '</div>';
			}
		}

		echo '</div>';
	}

}	
?>