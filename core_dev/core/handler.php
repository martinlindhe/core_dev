<?php
/**
 * $Id$
 *
 * core_dev handler
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('core.php');
require_once('db_mysqli.php');
require_once('files_default.php');
require_once('auth_default.php');
require_once('session_default.php');

class handler
{
	var $db      = false; ///< db driver in use
	var $user    = false; ///< user driver in use
	var $auth    = false; ///< auth driver in use
	var $session = false; ///< session driver in use
	var $files   = false; ///< files driver in use

	var $error;           ///< holds last error message. FIXME both auth->error and session->error exists aswell

	/**
	 * Constructor. Initializes the session class
	 *
	 * @param $conf array with session settings
	 */
	function __construct($conf = array())
	{
	}

	/**
	 * The parameterized factory method
	 */
	public static function factory($type, $driver, $conf = array())
	{
		$class = $type.'_'.$driver;
		if (require_once($class.'.php')) {
			return new $class($conf);
		} else {
			throw new Exception('Driver '.$class.' not found');
		}
	}

	/**
	 * Load db driver
	 */
	function db($driver = 'mysqli', $conf = array())
	{
		$this->db = $this->factory('db', $driver, $conf);

		//XXX remove this hack:
		global $db;
		$db = $this->db;

		return true;
	}

	/**
	 * Load user driver
	 */
	function user($driver = 'default', $conf = array())
	{
		$this->user = $this->factory('user', $driver, $conf);
		return true;
	}

	/**
	 * Load auth driver
	 */
	function auth($driver = 'default', $conf = array())
	{
		if (!$this->user) {
			die("FATAL ERRROR: cant add auth handler without a user handler!\n");
		}

		$this->auth = $this->factory('auth', $driver, $conf);
		return true;
	}

	/**
	 * Load session driver
	 */
	function session($driver = 'default', $conf = array())
	{
		if (!$this->user) {
			die("FATAL ERRROR: cant add session handler without a user handler!\n");
		}

		if (!$this->auth) {
			die("FATAL ERRROR: cant add session handler without a auth handler!\n");
		}

		$this->session = $this->factory('session', $driver, $conf);

		return true;
	}

	/**
	 * Load files driver
	 */
	function files($driver = 'default', $conf = array())
	{
		$this->files = $this->factory('files', $driver, $conf);

		return true;
	}

	function log($str, $level = LOGLEVEL_NOTICE)
	{
		dp("handler->log(): ".$str);
	}

	function handleEvents()
	{
		if ($this->user) $this->handleUserEvents();
		if ($this->auth) $this->handleAuthEvents();
		if ($this->session) $this->handleSessionEvents();
	}

	function handleUserEvents()
	{
		//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
		if (!$this->session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']) && ($this->auth->allow_registration || !Users::cnt())) {
			$preId = 0;
			if (!empty($_POST['preId']) && is_numeric($_POST['preId'])) $preId = $_POST['preId'];
			$check = $this->user->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL, $preId);
			if (is_numeric($check)) {
				Users::setPassword($check, $_POST['register_pwd'], $_POST['register_pwd'], $this->auth->sha1_key);
				if ($this->auth->mail_activate) {
					$this->auth->sendActivationMail($check);
				} else {
					$this->auth->login($_POST['register_usr'], $_POST['register_pwd']);
				}
			} else {
				$this->error = t('Registration failed').', '.$check;
			}
		}
	}

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents()
	{
		//FIXME verify this works:
		/*
		if ($this->ip && isBlocked(BLOCK_IP, $this->ip)) {
			die('You have been blocked from this site.');
		}
		if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		*/

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$this->session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd'])) {
			$data = $this->auth->login($_POST['login_usr'], $_POST['login_pwd']);
			if ($data) {
				$this->session->start($data['userId'], $data['userName'], $data['userMode']);

				//Update last login time
				Users::loginTime($this->session->id);

				//FIXME: move the sql somehwere else
				$this->db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$this->session->id.', IP='.$this->auth->ip.', userAgent="'.$this->db->escape($_SERVER['HTTP_USER_AGENT']).'"');

				addEvent(EVENT_USER_LOGIN, 0, $this->session->id);

				//Load custom theme
				if ($this->session->allow_themes && $this->user->userdata) {
					$this->session->theme = loadUserdataTheme($this->session->id, $this->session->default_theme);
				}

				$this->log('User logged in', LOGLEVEL_NOTICE);
				$this->session->startPage();
			} else {
				$this->error = t('Login failed');
			}
		}

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->session->id && $this->auth->check_ip && $this->auth->ip && ($this->auth->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			//$this->error = t('Client IP changed.');
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->auth->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->session->end();
			$this->session->errorPage();
		}
	}

	/**
	 * Handles session events, such as idle timeout check. called from the constructor
	 */
	function handleSessionEvents()
	{
		//force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
		if (!empty($config['no_session']) || !$this->session->id) return;

		//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
		if (isset($_GET['logout'])) {
			$this->auth->logout($this->session->id);
			$this->session->end();
			$this->log('User logged out', LOGLEVEL_NOTICE);
			$this->session->loggedOutStartPage();
		}

		//Logged in: Check user activity - log out inactive user
		if ($this->session->lastActive < (time()-$this->session->timeout)) {
			$this->log('Session timed out after '.(time()-$this->session->lastActive).' (timeout is '.($this->session->timeout).')', LOGLEVEL_NOTICE);
			$this->session->end();
			$this->session->error = t('Session timed out');
			$this->session->errorPage();
		}

		if (!$this->session->id) return;

		//Update last active timestamp
		Users::activeTime($this->session->id);
		$this->session->lastActive = time();
	}

}
?>
