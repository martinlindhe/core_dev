<?php
/**
 * $Id$
 *
 * Default session class.
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('session_base.php');

require_once('atom_ip.php');		//for IPv4_to_GeoIP()
require_once('atom_settings.php');	//for storing userdata
require_once('atom_logging.php');	//for logEntry()

class session_default extends session_base
{
	var $session_name = 'someSID';		///< default session name
	var $timeout = 86400;				///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
	var $online_timeout = 1800;			///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc
	//todo: make online_timeout configurable

	var $start_page = 'index.php';		///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
	var $logged_out_start_page = 'index.php';
	var $error_page = 'error.php';		///< redirects the user to this page (in $config['app']['web_root'] directory) to show errors

	//Aliases of $_SESSION[] variables
	var $error;					///< last error message
	var $id;						///< current user's user ID
	var $username;				///< username of current user
	var $mode;					///< usermode
	var $lastActive;				///< last active
	var $started;				///< timestamp of when the session started
	var $theme = '';				///< contains the currently selected theme
	var $referer = '';			///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
	var $log_pageviews = false;	///< logs page views to tblPageViews

	var $isWebmaster;			///< is user webmaster?
	var $isAdmin;				///< is user admin?
	var $isSuperAdmin;			///< is user superadmin?

	var $userModes = array(
		0 => 'Normal user',
		1 => 'Webmaster',
		2 => 'Admin',
		3 => 'Super admin'
	); ///< user modes

	var $default_theme = 'default.css';			///< default theme if none is choosen
	var $allow_themes = false;					///< allow themes?


	function __construct($conf = array())
	{
		if (isset($conf['name'])) $this->session_name = $conf['name'];
		if (isset($conf['timeout'])) $this->timeout = $conf['timeout'];
		if (isset($conf['start_page'])) $this->start_page = $conf['start_page'];
		if (isset($conf['error_page'])) $this->error_page = $conf['error_page'];
		if (isset($conf['allow_themes'])) $this->allow_themes = $conf['allow_themes'];

		ini_set('session.gc_maxlifetime', $this->timeout);
		session_name($this->session_name);
		session_start();

		if (!isset($_SESSION['started']) || !$_SESSION['started']) $_SESSION['started'] = time();
		if (!isset($_SESSION['error'])) $_SESSION['error'] = '';
		if (!isset($_SESSION['ip'])) $_SESSION['ip'] = 0;
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
		$this->id = &$_SESSION['id'];	//if id is set, also means that the user is logged in
		$this->username = &$_SESSION['username'];
		$this->mode = &$_SESSION['mode'];
		$this->lastActive = &$_SESSION['lastActive'];
		$this->isWebmaster = &$_SESSION['isWebmaster'];
		$this->isAdmin = &$_SESSION['isAdmin'];
		$this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
		$this->theme = &$_SESSION['theme'];
		$this->referer = &$_SESSION['referer'];

		if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) {	//FIXME map to $this->auth->ip
			$this->ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
		}
	}

	/**
	 * Sets up a session. Called from the auth class
	 *
	 * @param $_id user id
	 * @param $_username user name
	 * @param $_usermode user mode
	 */
	function start($_id, $_username, $_usermode)
	{
		global $config;
		$this->id = $_id;
		$this->username = $_username;
		$this->mode = $_usermode;		//0=normal user. 1=webmaster, 2=admin, 3=super admin
		$this->lastActive = time();

		if ($this->mode >= USERLEVEL_WEBMASTER) $this->isWebmaster = true;
		if ($this->mode >= USERLEVEL_ADMIN) $this->isAdmin = true;
		if ($this->mode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;
	}

	/**
	 * Kills the current session, clearing all session variables
	 */
	function end()
	{
		$this->started = 0;
		$this->username = '';
		$this->ip = 0;
		$this->mode = 0;
		$this->isWebmaster = false;
		$this->isAdmin = false;
		$this->isSuperAdmin = false;
		$this->theme = $this->default_theme;
		$this->referer = '';

		if (!$this->id) return;

		$this->id = 0;
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

//XXX below functions fit in a "auth" submodule:

	/**
	 * Locks registered users out from certain pages, such as registration page
	 */
	function requireLoggedOut()
	{
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
		if (empty($config['no_redirect'])) $this->referer = $_SERVER['REQUEST_URI'];
		$this->errorPage();
	}


	/**
	 * Locks normal users out from certain pages
	 */
	function requireWebmaster()
	{
		if ($this->isWebmaster) return;
		if (!$this->error) $this->error = t('The page you requested requires webmaster rights to view.');
		$this->errorPage();
	}

	/**
	 * Locks normal users & webmasters out from certain pages
	 */
	function requireAdmin()
	{
		if ($this->isAdmin) return;
		if (!$this->error) $this->error = t('The page you requested requires admin rights to view.');
		$this->errorPage();
	}

	/**
	 * Locks out everyone except for super-admin from certain pages
	 */
	function requireSuperAdmin()
	{
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
