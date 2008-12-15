<?php
/**
 * $Id$
 *
 *
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('class.DB_MySQLi.php');	//class DB_MySQLi
require_once('session_default.php');	//class session_default()
require_once('user_default.php');		//class user_default()
require_once('auth_default.php');		//class auth_default()

class Session
{
	var $db = false;	///< db driver in use
	var $sess = false;	///< session handler in use
	var $user = false;	///< user handler in use

	var $id = false;


	/**
	 * Constructor. Initializes the session class
	 *
	 * @param $conf array with session settings
	 */
	function __construct($conf = array())
	{
		//Load db driver
		if (empty($conf['db']['driver']) || $conf['session']['driver'] == 'mysqli') {
			$this->db = new DB_MySQLi($conf['db']);

			//XXX remove this hack:
			global $db;
			$db = $this->db;
		}

		//Load user driver
		if (empty($conf['user']['driver']) || $conf['user']['driver'] == 'default') {
			$this->user = new user_default($this->db, $conf['user']);
		}

		//Load auth driver
		if (empty($conf['auth']['driver']) || $conf['auth']['driver'] == 'default') {
			$this->auth = new auth_default($this->db, $conf['auth']);
		}

		//Load session driver
		if (empty($conf['session']['driver']) || $conf['session']['driver'] == 'default') {
			$this->sess = new session_default($this->db, $conf['session']);

			//XXX map all $this->sess->function to $this->function
			$this->id = $this->sess->id;
		}
	}

	function handleEvents()
	{
		if ($this->auth) $this->auth->handleAuthEvents($this->sess);
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
