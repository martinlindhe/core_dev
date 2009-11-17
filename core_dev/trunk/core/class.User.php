<?php
/**
 * $Id$
 *
 * User object
 */

//STATUS: not finished

class User
{
	private $tbl_name = 'tblUsers';

	private $id;

	function __construct($id = 0)
	{
		if ($id) $this->setId($id);
	}

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;
	}

	function getName()
	{
		global $db, $h;
		if (!$this->id) return false;

		if ($h && $this->id == $h->session->id) return $h->session->username;

		$q = 'SELECT userName FROM '.$this->tbl_name.' WHERE userId='.$this->id;
		return $db->getOneItem($q);
	}

	/**
	 * Generates a link to user's page
	 */
	function link($name = '', $class = '')
	{
		if (!$this->id) return t('Anonymous');
		if (!$name) $name = $this->getName();
		if (!$name) return t('User not found');

		return '<a '.($class?' class="'.$class.'"':'').'href="user.php?id='.$this->id.'">'.$name.'</a>';
	}



}

?>
