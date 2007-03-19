<?
/*
	Session class

	Written by Martin Lindhe, 2007
	
	Todo: 
		* gör färdigt login-bubblan
			- register new user måste fungera (kräver email fält i tblUsers, kräv email aktivering av konton)
			- forgot password måste fungera (kräver register new user)
		
	Examples:
		$session->save('kex', 'med blandade bullar');
		$kex = $session->read('kex');

*/

require_once('functions_ip.php');
require_once('functions_settings.php');

class Session
{
	private $session_name = 'sid';			//default session name
	private $timeout = 1800;						//max allowed idle time (in seconds) before auto-logout
	private $check_ip = true;						//client will be logged out if client ip is changed during the session, this can be overridden with _POST['login_lock_ip']
	private $sha1_key = 'rpxp8xkeWljo';	//used to further encode sha1 passwords, to make rainbow table attacks harder
	private $allow_registration = true;	//set to false to disallow the possibility to register new users

	//Aliases of $_SESSION[] variables
	public $error;
	public $ip;
	public $id;
	public $username;
	public $mode;
	public $lastActive;
	public $isAdmin;
	public $isSuperAdmin;
	public $started;		//timestamp of when the session started

	public function __construct(array $session_config)
	{
		if (isset($session_config['name'])) $this->session_name = $session_config['name'];
		if (isset($session_config['timeout'])) $this->timeout = $session_config['timeout'];
		if (isset($session_config['check_ip'])) $this->check_ip = $session_config['check_ip'];
		if (isset($session_config['sha1_key'])) $this->sha1_key = $session_config['sha1_key'];
		if (isset($session_config['allow_registration'])) $this->allow_registration = $session_config['allow_registration'];

		global $db;

		session_name($this->session_name);
		session_start();

		if (!isset($_SESSION['started']) || !$_SESSION['started']) $_SESSION['started'] = time();
		if (!isset($_SESSION['error'])) $_SESSION['error'] = '';
		if (!isset($_SESSION['ip'])) $_SESSION['ip'] = 0;
		if (!isset($_SESSION['id'])) $_SESSION['id'] = 0;
		if (!isset($_SESSION['username'])) $_SESSION['username'] = '';
		if (!isset($_SESSION['mode'])) $_SESSION['mode'] = 0;
		if (!isset($_SESSION['lastActive'])) $_SESSION['lastActive'] = 0;
		if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = 0;
		if (!isset($_SESSION['isSuperAdmin'])) $_SESSION['isSuperAdmin'] = 0;

		$this->started = &$_SESSION['started'];
		$this->error = &$_SESSION['error'];
		$this->ip = &$_SESSION['ip'];	//store IP as an unsigned 32bit int
		$this->id = &$_SESSION['id'];	//if id is set, also means that the user is logged in
		$this->username = &$_SESSION['username'];
		$this->mode = &$_SESSION['mode'];
		$this->lastActive = &$_SESSION['lastActive'];
		$this->isAdmin = &$_SESSION['isAdmin'];
		$this->isSuperAdmin = &$_SESSION['isSuperAdmin'];

		if (!$this->ip) $this->ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);

		//Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
				$this->error = 'Client IP changed';
				$db->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']));
				$this->logOut();
		}

		//Check user activity - log out inactive user
		if ($this->id) {
			if ($this->lastActive < (time()-$this->timeout)) {
				$this->error = 'Inactivity timeout';
				$db->log('Session timed out after '.(time()-$this->lastActive).' (timeout is '.($this->timeout).')');
				$this->logOut();
			} else {
				//Update last active timestamp
				$db->query('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->id);
				$this->lastActive = time();
			}
		}

		//Check for login/logout requests
		if (!$this->id && !empty($_POST['login_usr']) && !empty($_POST['login_pwd']))
		{
			//POST to any page with 'usr' & 'pwd' variables set to log in
			$this->logIn($_POST['login_usr'], $_POST['login_pwd']);

			//See what IP checking policy that will be in use for the session
			if (!empty($_POST['login_lock_ip'])) $this->check_ip = true;
			else $this->check_ip = false; 
		}
		else if ($this->id && isset($_GET['logout']))
		{
			//GET to any page with 'logout' set to log out
			$db->log('user logged out');
			$this->logOut();
			header('Location: '.basename($_SERVER['SCRIPT_NAME']));
			die;
		}

	}

	public function __destruct()
	{
	}

	public function logIn($username, $password)
	{
		global $db;
		
		$enc_username = $db->escape($username);
		$enc_password = sha1( sha1($this->sha1_key).sha1($password) );

		$data = $db->getOneRow('SELECT * FROM tblUsers WHERE userName="'.$enc_username.'" AND userPass="'.$enc_password.'"');
		if (!$data) {
			$this->error = 'Login failed';
			$db->log('failed login attempt: username '.$enc_username);
			return false;
		}

		$this->error = '';
		$this->username = $enc_username;
		$this->id = $data['userId'];
		$this->mode = $data['userMode'];		//0=normal user. 1=admin, 2=super admin

		if ($this->mode >= 1) $this->isAdmin = 1;
		if ($this->mode >= 2) $this->isSuperAdmin = 1;

		//Update last login time
		$db->query('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$this->id);
		$this->lastActive = time();

		//$db->log('user logged in');

		return true;
	}

	public function logOut()
	{
		global $db;

		$db->query('UPDATE tblUsers SET timeLastLogout=NOW()');
		$this->started = 0;
		$this->id = 0;
		$this->ip = 0;
		$this->mode = 0;
		$this->isAdmin = 0;
		$this->isSuperAdmin = 0;
	}
	
	public function showLoginForm()
	{
		echo '<div class="login_box">';

		echo '<div id="login_form_layer">';
		echo '<form name="login_form" method="post" action="">';
		if ($this->error) {
			echo '<b>Error: '.$this->error.'</b><br>';
			$this->error = ''; //remove error message once it has been displayed
		}
		echo '<table cellpadding=2>';
		echo '<tr><td>Username:</td><td><input name="login_usr" type="text"> <img src="/gfx/icon_user.png" align="absmiddle"></td></tr>';
		echo '<tr><td>Password:</td><td><input name="login_pwd" type="password"> <img src="/gfx/icon_keys.png" align="absmiddle"></td></tr>';
		echo '</table>';
		echo '<input id="login_lock_ip" name="login_lock_ip" value="1" type="checkbox" checked> ';
		echo '<label for="login_lock_ip">Restrict session to current IP</label><br>';
		echo '<br>';
		echo '<input type="submit" class="button" value="Log in">';
		if ($this->allow_registration) {
			echo '<input type="button" class="button" value="Register" onClick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_register_layer\');">';
			echo '<input type="button" class="button" value="Forgot password" onClick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');">';
		}
		echo '</form>';
		echo '</div>';
		
		if ($this->allow_registration) {
			echo '<div id="login_register_layer" style="display: none;">';
				echo '<b>Register new account</b><br><br>';

				echo '<table cellpadding=2>';
				echo '<tr><td>Username:</td><td><input name="register_usr" type="text"> <img src="/gfx/icon_user.png" align="absmiddle"></td></tr>';
				echo '<tr><td>Password:</td><td><input name="register_pwd" type="password"> <img src="/gfx/icon_keys.png" align="absmiddle"></td></tr>';
				echo '<tr><td>Again:</td><td><input name="register_pwd2" type="password"> <img src="/gfx/icon_keys.png" align="absmiddle"></td></tr>';
				echo '<tr><td>E-mail:</td><td><input name="register_email" type="password"> <img src="/gfx/icon_mail.png" align="absmiddle"></td></tr>';
				echo '</table><br>';

				echo '<input type="button" class="button" value="Log in" onClick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_form_layer\');">';
				echo '<input type="button" class="button" value="Register" disabled>';
				echo '<input type="button" class="button" value="Forgot password" onClick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');">';
			echo '</div>';

			//todo: javascript som validerar input email, visa en "retrieve new password" knapp om emailen är korrekt
			echo '<div id="login_forgot_pwd_layer" style="display: none;">';
				echo 'Enter the e-mail address used when registering your account.<br><br>';
				echo 'You will recieve an e-mail with a link to follow, where you can set a new password.<br><br>';
				echo '<table cellpadding=2>';
				echo '<tr><td>E-mail:</td><td><input type="text" size=26> <img src="/gfx/icon_mail.png" align="absmiddle"></td></tr>';
				echo '</table><br>';
				echo '<input type="submit" class="button" value="New password"><br><br>';

				echo '<input type="button" class="button" value="Log in" onClick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_form_layer\');">';
				echo '<input type="button" class="button" value="Register" onClick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_register_layer\');">';
				echo '<input type="button" class="button" value="Forgot password" disabled>';
			echo '</div>';
		}

		echo '</div>';
	}

	public function showInfo()
	{
		echo 'Logged in: '. ($this->id?'YES':'NO').'<br>';
		if ($this->id) {
			echo '<b>User name: '.$this->username.'</b><br>';
			echo '<b>User ID: '.$this->id.'</b><br>';
			echo '<b>User mode: '.$this->mode.'</b><br>';
		}
		echo 'Session name: '.$this->session_name.'<br>';
		echo 'Current IP: '.GeoIP_to_IPv4($this->ip).'<br>';
		echo 'Session timeout: '.$this->timeout.'<br>';
		echo 'Check for IP changes: '. ($this->check_ip?'YES':'NO').'<br>';
		if ($this->isSuperAdmin) {
			echo 'SHA1 key: '.$this->sha1_key.'<br>';
		}
	}

	/* Saves a setting associated with current user */
	public function save($name, $value)
	{
		saveSetting(SETTING_USER, $this->id, $name, $value);
	}

	/* Reads a setting associated with current user */
	public function read($name, $default = '')
	{
		return readSetting(SETTING_USER, $this->id, $name, $default);
	}

	/* Renders html for editing all tblSettings field for current user */
	//todo: use ajax to save changes
	//todo: define this in a separate /design/ file, with alot of css in /css/functions.css
	public function editSettings()
	{
		$list = readAllSettings(SETTING_USER, $this->id);
		if (!$list) return;

		echo '<div id="edit_settings" style="width: 300px; background-color: #88EE99; border: 1px solid #aaa; padding: 5px;">';
		echo '<div id="ajax_anim" style="display:none; float:right; background-color: #eee; padding: 5px; border: 1px solid #aaa;"><img id="ajax_anim_pic" alt="AJAX Loading ..." title="AJAX Loading ..." src="/gfx/ajax_loading.gif"></div>';
		echo '<form name="edit_settings_frm" action="">';
		for ($i=0; $i<count($list); $i++) {
			echo '<div id="edit_setting_div_'.$list[$i]['settingId'].'">';
			echo $list[$i]['settingName'].': <input type="text" name="edit_setting_'.$list[$i]['settingId'].'" value="'.$list[$i]['settingValue'].'">';
			echo '<img src="/gfx/icon_error.png" title="Delete" alt="Delete" onClick="perform_ajax_delete_uservar('.$list[$i]['settingId'].');">';
			echo '</div>';
		}
		echo '<input type="submit" value="Save" disabled>';
		echo '</form>';
		echo '</div>';
	}

}
?>