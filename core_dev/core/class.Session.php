<?
/*
	Session class

	Written by Martin Lindhe, 2007

	User setting examples:
		$session->save('variablename', 'some random setting to save');
		$kex = $session->read('variablename');
*/

require_once('functions_general.php');
require_once('functions_ip.php');
require_once('functions_textformat.php');
require_once('functions_userdata.php');
require_once('functions_users.php');

require_once('atom_moderation.php');	//for checking if username is reserved on user registration
require_once('atom_settings.php');	//for storing userdata

define('LOGLEVEL_NOTICE', 1);
define('LOGLEVEL_WARNING', 2);
define('LOGLEVEL_ERROR', 3);
define('LOGLEVEL_ALL', 5);

class Session
{
	private $session_name = 'someSID';	//default session name
	private $timeout = 86400;					//24h - max allowed idle time (in seconds) before session times out and user needs to log in again
	public $online_timeout = 1800;		//30m - max idle time before the user is counted as "logged out" in "users online"-lists etc
	//todo: make online_timeout configurable
	private $check_ip = true;						//client will be logged out if client ip is changed during the session
	private $check_useragent = true;		//keeps track if the client user agent string changes during the session

	private $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	//used to further encode sha1 passwords, to make rainbow table attacks harder
	public $allow_login = true;				//set to false to only let superadmins log in to the site
	private $allow_registration = true;	//set to false to disallow the possibility to register new users. will be disabled if login is disabled
	private $reserved_usercheck = true;		//check if username is listed as reserved username, requires tblStopwords
	private $userdata = true; //shall we use tblUserdata for required userdata fields?

	private $start_page = '';						//redirects user to this page (in $config['web_root'] directory) after successful login
	private $error_page = 'error.php';	//redirects the user to this page (in $config['web_root'] directory) to show errors

	//Aliases of $_SESSION[] variables
	public $error;
	public $ip;
	public $user_agent;			//current user's UserAgent string
	public $ua_ie;					//boolean true if the user is using internet explorer
	public $id;							//current user's user ID
	public $username;
	public $mode;
	public $lastActive;
	public $isAdmin;
	public $isSuperAdmin;
	public $started;		//timestamp of when the session started
	public $theme = '';			//contains the currently selected theme

	public $userModes = array(
		0 => 'Normal user',
		1 => 'Admin',
		2 => 'Super admin'
	);
	
	private $default_theme = 'default.css';			//default theme if none is choosen
	private $allow_themes = false;

	function __construct(array $session_config = array(''))
	{
		global $db, $config;

		if (isset($session_config['name'])) $this->session_name = $session_config['name'];
		if (isset($session_config['timeout'])) $this->timeout = $session_config['timeout'];
		if (isset($session_config['check_ip'])) $this->check_ip = $session_config['check_ip'];
		if (isset($session_config['check_useragent'])) $this->check_useragent = $session_config['check_useragent'];
		if (isset($session_config['sha1_key'])) $this->sha1_key = $session_config['sha1_key'];
		if (isset($session_config['allow_login'])) $this->allow_login = $session_config['allow_login'];
		if (isset($session_config['allow_registration'])) $this->allow_registration = $session_config['allow_registration'];
		if (isset($session_config['start_page'])) $this->start_page = $session_config['start_page'];
		if (isset($session_config['error_page'])) $this->error_page = $session_config['error_page'];
		if (isset($session_config['allow_themes'])) $this->allow_themes = $session_config['allow_themes'];
		if (isset($session_config['userdata'])) $this->userdata = $session_config['userdata'];
		if (isset($session_config['reserved_usercheck'])) $this->reserved_usercheck = $session_config['reserved_usercheck'];

		ini_set('session.gc_maxlifetime', $session_config['timeout']);
		session_name($this->session_name);
		session_start();

		if (!isset($_SESSION['started']) || !$_SESSION['started']) $_SESSION['started'] = time();
		if (!isset($_SESSION['error'])) $_SESSION['error'] = '';
		if (!isset($_SESSION['ip'])) $_SESSION['ip'] = 0;
		if (!isset($_SESSION['user_agent'])) $_SESSION['user_agent'] = '';
		if (!isset($_SESSION['id'])) $_SESSION['id'] = 0;
		if (!isset($_SESSION['username'])) $_SESSION['username'] = '';
		if (!isset($_SESSION['mode'])) $_SESSION['mode'] = 0;
		if (!isset($_SESSION['lastActive'])) $_SESSION['lastActive'] = 0;
		if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = 0;
		if (!isset($_SESSION['isSuperAdmin'])) $_SESSION['isSuperAdmin'] = 0;
		if (!isset($_SESSION['theme'])) $_SESSION['theme'] = $this->default_theme;

		$this->started = &$_SESSION['started'];
		$this->error = &$_SESSION['error'];
		$this->ip = &$_SESSION['ip'];
		$this->user_agent = &$_SESSION['user_agent'];
		$this->id = &$_SESSION['id'];	//if id is set, also means that the user is logged in
		$this->username = &$_SESSION['username'];
		$this->mode = &$_SESSION['mode'];
		$this->lastActive = &$_SESSION['lastActive'];
		$this->isAdmin = &$_SESSION['isAdmin'];
		$this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
		$this->theme = &$_SESSION['theme'];

		if (!$this->ip) $this->ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
		if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';

		//FIXME conditionally reuse some functions_browserstats.php features. make user agent parsing optional & disabled by default
		$this->ua_ie = false;
		if (strpos($this->user_agent, 'MSIE')) $this->ua_ie = true;	//FIXME this check will handle Opera as a IE browser
	}

	function handleSessionActions()
	{
		global $db, $config;

		//force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
		if (!empty($config['no_session'])) return;

		if (!$this->id && !empty($_POST['register_usr']) && !empty($_POST['register_pwd']) && !empty($_POST['register_pwd2'])) {
			$check = $this->registerUser($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']);
			if (!is_numeric($check)) {
				echo 'Registration failed: '.$check;
				die;
			}
			$this->logIn($_POST['register_usr'], $_POST['register_pwd']);
		}

		//Shows login form if 'login' variable is set
		if (!$this->id && isset($_GET['login'])) {
			$session = &$this;	//Required, else any use of $session in header/footer will be undefined references
			require('design_head.php');	//fixme: hur kan jag slippa detta
			$this->showLoginForm();
			require('design_foot.php');
			die;
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

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			$this->error = 'Client IP changed';
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->logOut();
		}

		//Logged in: Check user activity - log out inactive user
		if ($this->lastActive < (time()-$this->timeout)) {
			$this->error = 'Inactivity timeout';
			$this->log('Session timed out after '.(time()-$this->lastActive).' (timeout is '.($this->timeout).')', LOGLEVEL_NOTICE);
			$this->logOut();
		}

		//Logged in: Check if client user agent string changed, after active check to avoid useragent change log on auto browser upgrade (Firefox)
		if ($this->check_useragent && $this->user_agent && ($this->user_agent != $_SERVER['HTTP_USER_AGENT'])) {
			$this->error = 'Client user agent string changed';
			$this->log('Client user agent string changed from "'.$this->user_agent.'" to "'.$_SERVER['HTTP_USER_AGENT'].'"', LOGLEVEL_ERROR);
			$this->logOut();
		}

		if (!$this->id) return;

		//Update last active timestamp
		$db->query('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->id);
		$this->lastActive = time();
	}

	/* Writes a log entry to tblLogs */
	function log($str, $entryLevel = LOGLEVEL_NOTICE)
	{
		global $db;

		if (!is_numeric($entryLevel)) return false;

		$q = 'INSERT INTO tblLogs SET entryText="'.$db->escape($str).'",entryLevel='.$entryLevel.',timeCreated=NOW(),userId='.$this->id.',userIP='.$this->ip;
		return $db->insert($q);
	}


	//returns the user ID of the newly created user
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

	function logIn($username, $password)
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

	function logOut()
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

	/*Shows a login form with tabs for Register & Forgot password functions */
	//the handling of the result variables are called by $session->handleSessionActions() from config.php 
	function showLoginForm()
	{
		global $config;
		echo '<div class="login_box">';

		$allow_superadmin_reg = false;
		if (!getUsersCnt()) {
			echo 'No users registered!';
			$allow_superadmin_reg = true;
		}

		echo '<div id="login_form_layer">';
		if (!$this->allow_login) {
			echo '<div class="critical">Logins are currently not allowed.<br/>Please try again later.</div>';
		}
		echo '<form name="login_form" method="post" action="">';
		if ($this->error) {
			echo '<div class="critical"><img src="'.$config['core_web_root'].'gfx/icon_warning_big.png" alt="Error"/> '.$this->error.'</div>';
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
			echo '<input type="button" class="button" value="Forgot password" onclick="hide_element_by_name(\'login_form_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
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
				echo '<input type="button" class="button" value="Forgot password" onclick="hide_element_by_name(\'login_register_layer\'); show_element_by_name(\'login_forgot_pwd_layer\');"/>';
				echo '</form>';
			echo '</div>';

			//todo: javascript som validerar input email, visa en "retrieve new password" knapp om emailen är korrekt
			echo '<div id="login_forgot_pwd_layer" style="display: none;">';
				echo '<form method="post" action="">';
				echo 'Enter the e-mail address used when registering your account.<br/><br/>';
				echo 'You will recieve an e-mail with a link to follow, where you can set a new password.<br/><br/>';
				echo '<table cellpadding="2">';
				echo '<tr><td>E-mail:</td><td><input type="text" size="26"/> <img src="'.$config['core_web_root'].'gfx/icon_mail.png" alt="E-Mail"/></td></tr>';
				echo '</table><br/>';

				echo '<input type="button" class="button" value="Log in" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_form_layer\');"/>';
				echo '<input type="button" class="button" value="Register" onclick="hide_element_by_name(\'login_forgot_pwd_layer\'); show_element_by_name(\'login_register_layer\');"/>';
				echo '<input type="submit" class="button" value="Forgot password" style="font-weight: bold;"/>';
				echo '</form>';
			echo '</div>';
		}

		echo '</div>';
	}

	function showInfo()
	{
		global $config;

		echo '<b>Current session information</b><br/>';
		echo 'Logged in: '. ($this->id?'YES':'NO').'<br/>';
		if ($this->id) {
			echo 'User name: '.$this->username.'<br/>';
			echo 'User ID: '.$this->id.'<br/>';
		}

		echo 'User mode: ';
		if ($this->isSuperAdmin) echo 'Super admin<br/>';
		else if ($this->isAdmin) echo 'Admin<br/>';
		else if ($this->id) echo 'Normal user<br/>';
		else echo 'Visitor<br/>';

		echo 'Session name: '.$this->session_name.'<br/>';
		echo 'Current IP: '.GeoIP_to_IPv4($this->ip).'<br/>';
		echo 'User Agent: '.$this->user_agent.'<br/>';
		echo 'Session timeout: '.shortTimePeriod($this->timeout).'<br/>';
		echo 'Check for IP changes: '. ($this->check_ip?'YES':'NO').'<br/>';
		echo 'Start page: '.$config['web_root'].$this->start_page.'<br/>';
		echo 'Error page: '.$config['web_root'].$this->error_page.'<br/>';
		if ($this->isSuperAdmin) {
			echo 'SHA1 key: '.$this->sha1_key.'<br/>';
		}
	}

	/* Locks unregistered users out from certain pages */
	function requireLoggedIn()
	{
		global $config;
		if ($this->id) return;

		$this->error = 'The page you requested requires you to be logged in';
		header('Location: '.$config['web_root'].$this->error_page);
		die;
	}

	/* Locks unregistered users out from certain pages */
	function requireAdmin()
	{
		global $config;
		if ($this->isAdmin) return;

		$this->error = 'The page you requested requires admin rights to view';
		header('Location: '.$config['web_root'].$this->error_page);
		die;
	}

	/* Locks out everyone except for super-admin from certain pages */
	function requireSuperAdmin()
	{
		global $config;
		if ($this->isSuperAdmin) return;

		$this->error = 'The page you requested requires superadmin rights to view';
		header('Location: '.$config['web_root'].$this->error_page);
		die;
	}

	/* Locks out everyone not from localhost (for setup scripts) */
	function requireLocalhost()
	{
		if (GeoIP_to_IPv4($this->ip) == '127.0.0.1') return;
		die;
	}
}
?>
