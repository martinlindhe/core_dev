<?php
/**
 * $Id$
 *
 * Default user class
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

class user_default
{
	var $db = false;	///< points to $db driver to use

	function __construct($db = false, $conf = array())
	{
		$this->db = $db;
		//echo "___ user_default constructor!\n";
	}

	/**
	 * Handles logins
	 *
	 * @param $username
	 * @param $password
	 * @return true on success
	 */
	function login($username, $password)
	{
		global $db, $session;

		$data = $this->validLogin($username, $password);

		if (!$data) {
			$session->error = t('Login failed');
			$session->log('Failed login attempt: username '.$username, LOGLEVEL_WARNING);
			return false;
		}

		if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
			if ($this->mail_activate && !Users::isActivated($data['userId'])) {
				$this->error = t('This account has not yet been activated.');
				return false;
			}

			if (!$this->allow_login) {
				$this->error = t('Logins currently not allowed.');
				return false;
			}

			$blocked = isBlocked(BLOCK_USERID, $data['userId']);
			if ($blocked) {
				$this->error = t('Account blocked');
				$session->log('Login attempt from blocked user: username '.$username, LOGLEVEL_WARNING);
				return false;
			}
		}

		$session->startSession($data['userId'], $data['userName'], $data['userMode']);

		//Update last login time
		$db->update('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$session->id);
		$db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$session->id.', IP='.$session->ip.', userAgent="'.$db->escape($_SERVER['HTTP_USER_AGENT']).'"');

		addEvent(EVENT_USER_LOGIN, 0, $session->id);

		return true;
	}

	/**
	 * Checks if this is a valid login
	 *
	 * @return if valid login, return user data, else false
	 */
	function validLogin($username, $password)
	{
		global $db;

		$q = 'SELECT userId FROM tblUsers WHERE userName="'.$db->escape($username).'" AND timeDeleted IS NULL';
		$id = $db->getOneItem($q);
		if (!$id) return false;

		$enc_password = sha1( $id.sha1($this->sha1_key).sha1($password) );

 		$q = 'SELECT * FROM tblUsers WHERE userId='.$id.' AND userPass="'.$enc_password.'"';
 		$data = $db->getOneRow($q);

		return $data;
	}

	/**
	 * Logs out the user
	 */
	function logout()
	{
		global $session;
		$this->db->update('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$session->id);

		addEvent(EVENT_USER_LOGOUT, 0, $session->id);
		$session->endSession();
	}

}

?>
