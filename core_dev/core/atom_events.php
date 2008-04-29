<?
/**
 * $Id$
 *
 * Generic event logging functions
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	define('EVENT_M2W_CALL_BEGIN',	0x50);
	define('EVENT_M2W_CALL_END',	0x51);
	define('EVENT_RECORDED_PRES',	0x52);
	define('EVENT_RECORDED_MSG',	0x53);
	define('EVENT_RECORDED_BLOG',	0x54);
	define('EVENT_WARNED',			0x55);
	define('EVENT_PRES_APPROVED',	0x56);
	define('EVENT_PRES_DENIED',		0x57);

	function addEvent($_type, $ownerId)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

		$q = 'INSERT INTO tblEvents SET type='.$_type.', ownerId='.$ownerId.',timeCreated=NOW()';
		return $db->insert($q);
	}

?>
