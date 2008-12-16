<?php
/**
 * $Id$
 *
 *
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('db_mysqli.php');			//class db_mysqli
require_once('session_default.php');	//class session_default
require_once('user_default.php');		//class user_default
require_once('auth_default.php');		//class auth_default

class Handler	//core_dev handler
{
	var $db = false;	///< db driver in use
	var $user = false;	///< user driver in use
	var $auth = false;	///< auth driver in use
	var $sess = false;	///< session driver in use

	//var $id = false;

	/**
	 * Constructor. Initializes the session class
	 *
	 * @param $conf array with session settings
	 */
	function __construct($conf = array())
	{
		//Load db driver
		if (!empty($conf['db']['driver'])) {
			$this->db = $this->factory('db', $conf['db']['driver'], $conf['db']);
			$this->db->par = &$this;	//XXX hack to allow access to parent class

			//XXX remove this hack:
			global $db;
			$db = $this->db;
		}

		//Load user driver
		if (!empty($conf['user']['driver'])) {
			$this->user = $this->factory('user', $conf['user']['driver'], $conf['user']);
			$this->user->par = &$this;	//XXX hack to allow access to parent class
		}

		//Load auth driver
		if (!empty($conf['auth']['driver'])) {
			$this->auth = $this->factory('auth', $conf['auth']['driver'], $conf['auth']);
			$this->auth->par = &$this;	//XXX hack to allow access to parent class
		}

		//Load session driver
		if (!empty($conf['session']['driver'])) {
			$this->sess = $this->factory('session', $conf['session']['driver'], $conf['session']);
			$this->sess->par = &$this;	//XXX hack to allow access to parent class

			//XXX map all $this->sess->function to $this->function
			$this->id = $this->sess->id;
		}
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

	function handleEvents()
	{
		if ($this->auth) $this->auth->handleAuthEvents($this->sess, $this->user);
		if ($this->sess) $this->sess->handleSessionEvents();
	}

	function log($str, $level = LOGLEVEL_NOTICE)
	{
		return $this->sess->log($str, $level);
	}

	function save($settingName, $settingValue, $categoryId = 0)
	{
		return saveSetting(SETTING_USERDATA, $categoryId, $this->id, $settingName, $settingValue);
	}

	function load($settingName, $defaultValue, $categoryId = 0)
	{
		return loadSetting(SETTING_USERDATA, $categoryId, $this->id, $settingName, $defaultValue);
	}

}
?>
