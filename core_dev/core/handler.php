<?php
/**
 * $Id$
 *
 * core_dev base class
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('core.php');
require_once('db_mysqli.php');			//class db_mysqli
require_once('user_default.php');		//class user_default
require_once('auth_default.php');		//class auth_default
require_once('session_default.php');	//class session_default

class handler
{
	var $db      = false; ///< db driver in use
	var $user    = false; ///< user driver in use
	var $auth    = false; ///< auth driver in use
	var $session = false; ///< session driver in use
	var $files   = false; ///< files driver in use

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

	function db($driver = 'mysqli', $conf = array())
	{
		//Load db driver
		$this->db = $this->factory('db', $driver, $conf);

		//XXX remove this hack:
		global $db;
		$db = $this->db;

		return true;
	}

	function user($driver = 'default', $conf = array())
	{
		//Load user driver
		$this->user = $this->factory('user', $driver, $conf);
		$this->user->par = &$this;	//XXX hack to allow access to parent class

		return true;
	}

	function auth($driver = 'default', $conf = array())
	{
		if (!$this->user) {
			die("FATAL ERRROR: cant add auth handler without a user handler!\n");
		}

		//Load auth driver
		$this->auth = $this->factory('auth', $driver, $conf);
		$this->auth->par = &$this;	//XXX hack to allow access to parent class

		return true;
	}

	function session($driver = 'default', $conf = array())
	{
		if (!$this->user) {
			die("FATAL ERRROR: cant add session handler without a user handler!\n");
		}

		if (!$this->auth) {
			die("FATAL ERRROR: cant add session handler without a auth handler!\n");
		}

		//Load session driver
		$this->session = $this->factory('session', $driver, $conf);
		$this->session->par = &$this;	//XXX hack to allow access to parent class

		return true;
	}

	function files($driver = 'default', $conf = array())
	{
		$this->files = $this->factory('files', $driver, $conf);
		$this->files->par = &$this;	//XXX hack to allow access to parent class

		return true;
	}

	function log($str, $level = LOGLEVEL_NOTICE)
	{
		dp("handler->log(): ".$str);
	}

	function handleEvents()
	{
		if ($this->auth) $this->handleAuthEvents();
		if ($this->session) $this->handleSessionEvents();
	}

	/**
	 * Handles login, logout & register user requests
	 */
	function handleAuthEvents()
	{
		/* 	//FIXME verify this works:
		if ($this->ip && isBlocked(BLOCK_IP, $this->ip)) {
			die('You have been blocked from this site.');
		}
		if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		*/

		//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
		if (!$this->session->id) {
			if (!empty($_POST['login_usr']) && isset($_POST['login_pwd']) && $this->auth->login($_POST['login_usr'], $_POST['login_pwd'])) {
				$this->log('User logged in', LOGLEVEL_NOTICE);
				$this->session->startPage();
			}
		}

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->auth->check_ip && $this->auth->ip && ($this->auth->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			//$this->error = t('Client IP changed.');
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->auth->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->session->end();
			$this->session->errorPage();
		}

		//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
		if (($this->auth->allow_registration || !Users::cnt()) && !$this->session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2'])) {
			$preId = 0;
			if (!empty($_POST['preId']) && is_numeric($_POST['preId'])) $preId = $_POST['preId'];
			$check = $this->auth->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL, $preId);
			if (is_numeric($check)) {
				if ($this->auth->mail_activate) {
					$this->auth->sendActivationMail($check);
				} else {
					$this->auth->login($_POST['register_usr'], $_POST['register_pwd']);
				}
			} else {
				$this->error = t('Registration failed').', '.$check;
			}
		}

		//Check if client user agent string changed
		if ($this->auth->check_useragent && $this->auth->user_agent != $_SERVER['HTTP_USER_AGENT']) {
			//FIXME this breaks when Firefox autoupdates & restarts
			//FIXME this occured once for a IE7 user while using embedded WMP11 + core_dev:
			//	"Client user agent string changed from "Windows-Media-Player/11.0.5721.5145" to "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)""
			//	also, this could be triggered if user is logged in & Firefox decides to auto-upgrade and restore previous tabs and sessions after restart

			$this->error = t('Client user agent string changed.');
			$this->log('Client user agent string changed from "'.$this->auth->user_agent.'" to "'.$_SERVER['HTTP_USER_AGENT'].'"', LOGLEVEL_ERROR);
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
			$this->auth->logout();
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
		//FIXME: move this SQL command to auth or user class!
		$this->db->update('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->session->id);
		$this->session->lastActive = time();
	}






















/*
	function save($settingName, $settingValue, $categoryId = 0)
	{
		return saveSetting(SETTING_USERDATA, $categoryId, $this->id, $settingName, $settingValue);
	}

	function load($settingName, $defaultValue, $categoryId = 0)
	{
		return loadSetting(SETTING_USERDATA, $categoryId, $this->id, $settingName, $defaultValue);
	}
*/
}
?>
