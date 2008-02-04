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
	abstract function registerUser($username, $password1, $password2, $userMode = 0);

	abstract function login($username, $password);

	abstract function logout();

	abstract function showLoginForm();

	private $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';	///< used to further encode sha1 passwords, to make rainbow table attacks harder

	public $allow_login = true;						///< set to false to only let superadmins log in to the site
	private $allow_registration = true;		///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
	private $reserved_usercheck = true;		///< check if username is listed as reserved username, requires tblStopwords
	private $userdata = true; 						///< shall we use tblUserdata for required userdata fields?

	function __construct(array $session_config = array(''))
	{
		global $db, $config;

		if (isset($session_config['sha1_key'])) $this->sha1_key = $session_config['sha1_key'];
		if (isset($session_config['allow_login'])) $this->allow_login = $session_config['allow_login'];
		if (isset($session_config['allow_registration'])) $this->allow_registration = $session_config['allow_registration'];
		if (isset($session_config['userdata'])) $this->userdata = $session_config['userdata'];
		if (isset($session_config['reserved_usercheck'])) $this->reserved_usercheck = $session_config['reserved_usercheck'];

	}
}
?>