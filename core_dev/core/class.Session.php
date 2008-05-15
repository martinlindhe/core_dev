<?php
/**
 * $Id$
 *
 * Session handling class
 *
 * Uses tblLogs to store session events
 *
 * User setting examples:
 *   $session->save('variablename', 'some random setting to save');
 *   $kex = $session->read('variablename');
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_ip.php');
require_once('atom_settings.php');	//for storing userdata
require_once('atom_blocks.php');	//for isBlocked()

define('LOGLEVEL_NOTICE',	1);
define('LOGLEVEL_WARNING',	2);
define('LOGLEVEL_ERROR',	3);
define('LOGLEVEL_ALL',		5);

define('USERLEVEL_NORMAL',		0);
define('USERLEVEL_WEBMASTER',	1);
define('USERLEVEL_ADMIN',		2);
define('USERLEVEL_SUPERADMIN',	3);

class Session
{
	private $session_name = 'someSID';		///< default session name
	private $timeout = 86400;				///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
	public $online_timeout = 1800;			///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc
	//todo: make online_timeout configurable
	private $check_ip = true;				///< client will be logged out if client ip is changed during the session
	private $check_useragent = true;		///< keeps track if the client user agent string changes during the session

	private $start_page = 'index.php';		///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
	private $logged_out_start_page = 'index.php';
	private $error_page = 'error.php';		///< redirects the user to this page (in $config['app']['web_root'] directory) to show errors

	//Aliases of $_SESSION[] variables
	public $error;				///< last error message
	public $ip;					///< IP of current user
	public $user_agent;			///< current user's UserAgent string
	public $ua_ie;				///< boolean true if the user is using internet explorer
	public $id;					///< current user's user ID
	public $username;			///< username of current user
	public $mode;				///< usermode
	public $lastActive;			///< last active
	public $started;			///< timestamp of when the session started
	public $theme = '';			///< contains the currently selected theme
	public $referer = '';		///< redirects the user to this page after login

	public $isWebmaster;		///< is user webmaster?
	public $isAdmin;			///< is user admin?
	public $isSuperAdmin;		///< is user superadmin?

	public $userModes = array(
		0 => 'Normal user',
		1 => 'Webmaster',
		2 => 'Admin',
		3 => 'Super admin'
	); ///< user modes

	private $default_theme = 'default.css';			///< default theme if none is choosen
	private $allow_themes = false;					///< allow themes?

	/**
	 * Constructor. Initializes the session class
	 *
	 * \param $conf array with session settings
	 */
	function __construct(array $conf = array())
	{
		global $db, $config;

		if (isset($conf['name'])) $this->session_name = $conf['name'];
		if (isset($conf['timeout'])) $this->timeout = $conf['timeout'];
		if (isset($conf['check_ip'])) $this->check_ip = $conf['check_ip'];
		if (isset($conf['check_useragent'])) $this->check_useragent = $conf['check_useragent'];
		if (isset($conf['start_page'])) $this->start_page = $conf['start_page'];
		if (isset($conf['error_page'])) $this->error_page = $conf['error_page'];
		if (isset($conf['allow_themes'])) $this->allow_themes = $conf['allow_themes'];

		ini_set('session.gc_maxlifetime', $this->timeout);
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
		if (!isset($_SESSION['isWebmaster'])) $_SESSION['isWebmaster'] = 0;
		if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = 0;
		if (!isset($_SESSION['isSuperAdmin'])) $_SESSION['isSuperAdmin'] = 0;
		if (!isset($_SESSION['theme'])) $_SESSION['theme'] = $this->default_theme;
		if (!isset($_SESSION['referer'])) $_SESSION['referer'] = '';

		$this->started = &$_SESSION['started'];
		$this->error = &$_SESSION['error'];
		$this->ip = &$_SESSION['ip'];
		$this->user_agent = &$_SESSION['user_agent'];
		$this->id = &$_SESSION['id'];	//if id is set, also means that the user is logged in
		$this->username = &$_SESSION['username'];
		$this->mode = &$_SESSION['mode'];
		$this->lastActive = &$_SESSION['lastActive'];
		$this->isWebmaster = &$_SESSION['isWebmaster'];
		$this->isAdmin = &$_SESSION['isAdmin'];
		$this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
		$this->theme = &$_SESSION['theme'];
		$this->referer = &$_SESSION['referer'];

		if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) {
			$ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
			if (isBlocked(BLOCK_IP, $ip)) {
				die('You have been blocked from this site.');
			}
			$this->ip = $ip;
		}
		if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

		//FIXME conditionally reuse some functions_browserstats.php features. make user agent parsing optional & disabled by default
		$this->ua_ie = false;
		if (strpos($this->user_agent, 'MSIE')) $this->ua_ie = true;	//FIXME this check will handle Opera as a IE browser

		$this->handleSessionEvents();
	}

	/**
	 * Sets up a session. Called from the auth class
	 *
	 * \param $_id user id
	 * \param $_username user name
	 * \param $_usermode user mode
	 */
	function startSession($_id, $_username, $_usermode)
	{
		$this->id = $_id;
		$this->username = $_username;
		$this->mode = $_usermode;		//0=normal user. 1=webmaster, 2=admin, 3=super admin
		$this->lastActive = time();

		if ($this->mode >= USERLEVEL_WEBMASTER) $this->isWebmaster = true;
		if ($this->mode >= USERLEVEL_ADMIN) $this->isAdmin = true;
		if ($this->mode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

		/* Read in current users settings */
		if ($this->allow_themes) {
			$this->theme = loadUserdataTheme($this->id, $this->default_theme);
		}

		$this->log('User logged in', LOGLEVEL_NOTICE);
	}

	/**
	 * Kills the current session, clearing all session variables
	 */
	function endSession()
	{
		$this->started = 0;
		$this->username = '';
		$this->ip = 0;
		$this->user_agent = '';
		$this->mode = 0;
		$this->isWebmaster = false;
		$this->isAdmin = false;
		$this->isSuperAdmin = false;
		$this->theme = $this->default_theme;
		$this->referer = '';

		if (!$this->id) return;

		$this->id = 0;

		$this->log('User logged out', LOGLEVEL_NOTICE);
	}

	/**
	 * Handles session events, such as idle timeout check. called from the constructor
	 */
	function handleSessionEvents()
	{
		global $db, $config;

		//force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
		if (!empty($config['no_session']) || !$this->id) return;

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			$this->error = t('Client IP changed.');
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
		}

		//Logged in: Check user activity - log out inactive user
		if ($this->lastActive < (time()-$this->timeout)) {
			$this->error = t('Inactivity timeout.');
			$this->log('Session timed out after '.(time()-$this->lastActive).' (timeout is '.($this->timeout).')', LOGLEVEL_NOTICE);
			$this->endSession();
			$this->errorPage();
		}

		//Logged in: Check if client user agent string changed, after active check to avoid useragent change log on auto browser upgrade (Firefox)
		if ($this->check_useragent && $this->user_agent && ($this->user_agent != $_SERVER['HTTP_USER_AGENT'])) {
			$this->error = t('Client user agent string changed.');
			$this->log('Client user agent string changed from "'.$this->user_agent.'" to "'.$_SERVER['HTTP_USER_AGENT'].'"', LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
		}

		if (!$this->id) return;

		//Update last active timestamp
		$db->update('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->id);
		$this->lastActive = time();
	}

	/**
	 * Writes a log entry to tblLogs
	 *
	 * \param $str text to log
	 * \param $entryLevel type of log entry
	 */
	function log($str, $entryLevel = LOGLEVEL_NOTICE)
	{
		global $db;
		if (!is_numeric($entryLevel)) return false;

		$q = 'INSERT INTO tblLogs SET entryText="'.$db->escape($str).'",entryLevel='.$entryLevel.',timeCreated=NOW(),userId='.$this->id.',userIP='.$this->ip;
		return $db->insert($q);
	}

	/**
	 * Displays session error
	 */
	function showError()
	{
		global $config;

		if (!$this->error) {
			echo '<div class="okay">'.t('No errors to display.').'</div>';
			return;
		}

		echo '<div class="critical">'.$this->error.'</div>';

		$this->error = '';

	}

	/**
	 * Shows info about the session
	 */
	function showInfo()
	{
		global $config;

		echo '<b>Current session information</b><br/>';
		echo 'Logged in: '. ($this->id?'YES':'NO').'<br/>';
		if ($this->id) {
			echo 'User name: '.$this->username.'<br/>';
			echo 'User ID: '.$this->id.'<br/>';
		}

		echo 'User mode: ';	//FIXME: use $session->userModes
		if ($this->isSuperAdmin) echo 'Super admin<br/>';
		else if ($this->isAdmin) echo 'Admin<br/>';
		else if ($this->isWebmaster) echo 'Webmaster<br/>';
		else if ($this->id) echo 'Normal user<br/>';
		else echo 'Visitor<br/>';

		echo 'Session name: '.$this->session_name.'<br/>';
		echo 'Current IP: '.GeoIP_to_IPv4($this->ip).'<br/>';
		echo 'User Agent: '.$this->user_agent.'<br/>';
		echo 'Session timeout: '.shortTimePeriod($this->timeout).'<br/>';
		echo 'Check for IP changes: '. ($this->check_ip?'YES':'NO').'<br/>';
		echo 'Start page: '.$config['app']['web_root'].$this->start_page.'<br/>';
		echo 'Error page: '.$config['app']['web_root'].$this->error_page.'<br/>';
	}

	/**
	 * Redirects user to default start page (logged in)
	 */
	function startPage()
	{
		global $config;

		if (!empty($this->referer)) {
			header('Location: '.$this->referer);
		} else {
			header('Location: '.$config['app']['web_root'].$this->start_page);
		}
		die;
	}

	/**
	 * Redirects user to default start page (logged out)
	 */
	function loggedOutStartPage()
	{
		global $config;
		header('Location: '.$config['app']['web_root'].$this->logged_out_start_page);
		die;
	}

	/**
	 * Redirects user to error page
	 */
	function errorPage()
	{
		global $config;
		header('Location: '.$config['app']['web_root'].$this->error_page);
		die;
	}

	/**
	 * Locks registered users out from certain pages, such as registration page
	 */
	function requireLoggedOut()
	{
		global $config;
		if (!$this->id) return;
		$this->startPage();
	}

	/**
	 * Locks unregistered users out from certain pages
	 */
	function requireLoggedIn()
	{
		global $config;
		if ($this->id) return;
		if (!$this->error) $this->error = t('The page you requested requires you to be logged in.');
		$this->referer = $_SERVER['REQUEST_URI'];
		$this->errorPage();
	}


	/**
	 * Locks normal users out from certain pages
	 */
	function requireWebmaster()
	{
		global $config;
		if ($this->isWebmaster) return;
		if (!$this->error) $this->error = t('The page you requested requires webmaster rights to view.');
		$this->errorPage();
	}

	/**
	 * Locks normal users & webmasters out from certain pages
	 */
	function requireAdmin()
	{
		global $config;
		if ($this->isAdmin) return;
		if (!$this->error) $this->error = t('The page you requested requires admin rights to view.');
		$this->errorPage();
	}

	/**
	 * Locks out everyone except for super-admin from certain pages
	 */
	function requireSuperAdmin()
	{
		global $config;
		if ($this->isSuperAdmin) return;
		if (!$this->error) $this->error = t('The page you requested requires superadmin rights to view.');
		$this->errorPage();
	}

	/**
	 * Locks out everyone not from localhost (for setup scripts)
	 */
	function requireLocalhost()
	{
		if (GeoIP_to_IPv4($this->ip) == '127.0.0.1') return;
		die;
	}
}
?>
