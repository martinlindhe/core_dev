<?

/**
 * $Id$
 *
 * Code to implement activation procedures
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('ACTIVATE_CAPTCHA',	1);
define('ACTIVATE_EMAIL',		2);
define('ACTIVATE_SMS',			3);

	/**
	 *
	 * \param $_answer only used for CAPTCHA's
	 */
	function verifyActivation($_type, $_rnd, $_answer = '')
	{
		global $db;
		if (!is_numeric($_type)) return false;
		
		//TODO: verify that timeCreated isnt too old
		$q = 'SELECT COUNT(entryId) FROM tblActivation WHERE type='.$_type.' AND rnd="'.$db->escape($_rnd).'"';
		if ($_answer) $q .= ' AND answer="'.$db->escape($_answer).'"';
		return $db->getOneItem($q);
	}

	function createActivation($_type, $_rnd, $_answer = '')
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q = 'INSERT INTO tblActivation SET type='.$_type.',rnd="'.$db->escape($_rnd).'",timeCreated=NOW()';
		if ($_answer) $q .= ', answer="'.$db->escape($_answer).'"';
		return $db->insert($q);
	}
?>