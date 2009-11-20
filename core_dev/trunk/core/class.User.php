<?php
/**
 * $Id$
 *
 * User object
 */

//STATUS: not finished

require_once('class.CoreBase.php');

class User extends CoreBase
{
	private $id;
	private $ip;

	function __construct($id = 0)
	{
		if ($id) $this->setId($id);
	}

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;
	}

	function setIP($ip)
	{
		if (is_numeric($ip))
			$ip = GeoIP_to_IPv4($ip);

		$this->ip = $ip;
	}

	function getName()
	{
		global $db, $h;
		if (!$this->id) return false;

		if ($h && $this->id == $h->session->id) return $h->session->username;

		$q = 'SELECT userName FROM tblUsers WHERE userId='.$this->id;
		return $db->getOneItem($q);
	}

	/**
	 * Generates a link to user's page
	 */
	function link($name = '', $class = '')
	{
		if (!$this->id)
			return t('Anonymous');

		if (!$name)
			$name = $this->getName();

		if (!$name)
			return t('User not found');

		return '<a '.($class?' class="'.$class.'"':'').'href="user.php?id='.$this->id.'">'.$name.'</a>';
	}

	/**
	 * Returns a short description of the user
	 */
	function htmlSummary()
	{
		global $h;

		$res = $this->link();
		if ($h->session->isAdmin)
			$res .= ' ('.$this->ip.')';

		return $res;
	}

}

?>
