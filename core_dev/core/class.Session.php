<?
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

define('LOGLEVEL_NOTICE', 1);
define('LOGLEVEL_WARNING', 2);
define('LOGLEVEL_ERROR', 3);
define('LOGLEVEL_ALL', 5);

class Session
{
	private $session_name = 'someSID';	///< default session name
	private $timeout = 86400;						///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
	public $online_timeout = 1800;			///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc
	//todo: make online_timeout configurable
	private $check_ip = true;						///< client will be logged out if client ip is changed during the session
	private $check_useragent = true;		///< keeps track if the client user agent string changes during the session

	private $start_page = '';							///< redirects user to this page (in $config['web_root'] directory) after successful login
	private $error_page = 'error.php';		///< redirects the user to this page (in $config['web_root'] directory) to show errors

	//Aliases of $_SESSION[] variables
	public $error;					///< last error message
	public $ip;							///< IP of current user
	public $user_agent;			///< current user's UserAgent string
	public $ua_ie;					///< boolean true if the user is using internet explorer
	public $id;							///< current user's user ID
	public $username;				///< username of current user
	public $mode;						///< usermode
	public $lastActive;			///< last active
	public $isAdmin;				///< is user admin?
	public $isSuperAdmin;		///< is user superadmin?
	public $started;				///< timestamp of when the session started
	public $theme = '';			///< contains the currently selected theme

	public $userModes = array(
		0 => 'Normal user',
		1 => 'Admin',
		2 => 'Super admin'
	); ///< user modes
	
	private $default_theme = 'default.css';			///< default theme if none is choosen
	private $allow_themes = false;							///< allow themes?

	/**
	 * Constructor. Initializes the session class
	 *
	 * \param $session_conf array with session settings
	 */
	function __construct(array $session_conf = array())
	{
		global $db, $config;

		if (isset($session_conf['name'])) $this->session_name = $session_conf['name'];
		if (isset($session_conf['timeout'])) $this->timeout = $session_conf['timeout'];
		if (isset($session_conf['check_ip'])) $this->check_ip = $session_conf['check_ip'];
		if (isset($session_conf['check_useragent'])) $this->check_useragent = $session_conf['check_useragent'];
		if (isset($session_conf['start_page'])) $this->start_page = $session_conf['start_page'];
		if (isset($session_conf['error_page'])) $this->error_page = $session_conf['error_page'];
		if (isset($session_conf['allow_themes'])) $this->allow_themes = $session_conf['allow_themes'];

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
		$this->error = '';
		$this->id = $_id;
		$this->username = $_username;
		$this->mode = $_usermode;		//0=normal user. 1=admin, 2=super admin
		$this->lastActive = time();

		if ($this->mode >= 1) $this->isAdmin = 1;
		if ($this->mode >= 2) $this->isSuperAdmin = 1;

		/* Read in current users settings */
		if ($this->allow_themes) {
			$this->theme = loadUserdataSetting($this->id, 'Theme', $this->default_theme);
		}

		$this->log('User logged in', LOGLEVEL_NOTICE);
	}

	/**
	 * Kills the current session, clearing all session variables
	 */
	function endSession()
	{
		if (!$this->id) return;

		$this->log('User logged out', LOGLEVEL_NOTICE);

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
	 * Handles session events, such as idle timeout check. called from the constructor
	 */
	function handleSessionEvents()
	{
		global $db, $config;

		//force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
		if (!empty($config['no_session']) || !$this->id) return;

		//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
			$this->error = 'Client IP changed';
			$this->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']), LOGLEVEL_ERROR);
			$this->endSession();
			$this->errorPage();
		}

		//Logged in: Check user activity - log out inactive user
		if ($this->lastActive < (time()-$this->timeout)) {
			$this->error = 'Inactivity timeout';
			$this->log('Session timed out after '.(time()-$this->lastActive).' (timeout is '.($this->timeout).')', LOGLEVEL_NOTICE);
			$this->endSession();
			$this->errorPage();
		}

		//Logged in: Check if client user agent string changed, after active check to avoid useragent change log on auto browser upgrade (Firefox)
		if ($this->check_useragent && $this->user_agent && ($this->user_agent != $_SERVER['HTTP_USER_AGENT'])) {
			$this->error = 'Client user agent string changed';
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
			echo '<div class="okay">No errors to display</div>';
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
	}

	/**
	 * Redirects user to default start page
	 */
	function startPage()
	{
		global $config;
		header('Location: '.$config['web_root'].$this->start_page);
		die;
	}


	/**
	 * Redirects user to error page
	 */
	function errorPage()
	{
		global $config;
		header('Location: '.$config['web_root'].$this->error_page);
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
		$this->error = 'The page you requested requires you to be logged in';
		$this->errorPage();
	}

	/**
	 * Locks unregistered users out from certain pages
	 */
	function requireAdmin()
	{
		global $config;
		if ($this->isAdmin) return;
		$this->error = 'The page you requested requires admin rights to view';
		$this->errorPage();
	}

	/**
	 * Locks out everyone except for super-admin from certain pages
	 */
	function requireSuperAdmin()
	{
		global $config;
		if ($this->isSuperAdmin) return;
		$this->error = 'The page you requested requires superadmin rights to view';
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