<?
/**
 * $Id$
 *
 * Skeleton for authentication modules
 *
 * \todo a libapache2-mod-auth-openid module
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

abstract class Auth_Base
{
	public $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	///< used to further encode sha1 passwords, to make rainbow table attacks harder

	public $allow_login = true;						///< set to false to only let superadmins log in to the site
	public $allow_registration = true;		///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
	public $reserved_usercheck = true;		///< check if username is listed as reserved username, requires tblStopwords
	public $userdata = true; 							///< shall we use tblUserdata for required userdata fields?

	function __construct(array $auth_conf = array(''))
	{
		global $db;

		if (isset($auth_conf['sha1_key'])) $this->sha1_key = $auth_conf['sha1_key'];
		if (isset($auth_conf['allow_login'])) $this->allow_login = $auth_conf['allow_login'];
		if (isset($auth_conf['allow_registration'])) $this->allow_registration = $auth_conf['allow_registration'];
		if (isset($auth_conf['userdata'])) $this->userdata = $auth_conf['userdata'];
		if (isset($auth_conf['reserved_usercheck'])) $this->reserved_usercheck = $auth_conf['reserved_usercheck'];

		$this->handleAuthEvents();
	}

	abstract function registerUser($username, $password1, $password2, $userMode = 0);

	abstract function login($username, $password);

	abstract function logout();

	//FIXME abstract function unregisterUser()

	abstract function showLoginForm();

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents()
	{
		global $config, $session;

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$session->id) {
			if (isset($_POST['login_usr']) && isset($_POST['login_pwd']) && $this->login($_POST['login_usr'], $_POST['login_pwd'])) {
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
				//FIXME: implement activation
				$this->login($_POST['register_usr'], $_POST['register_pwd']);
			} else {
				$session->error = 'Registration failed, '.$check;
			}
		}

	}

}
?>