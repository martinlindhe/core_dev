<?
/**
 * $Id$
 *
 * Generic event logging functions
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */
	define('EVENT_USER_LOGIN',		0x01);
	define('EVENT_USER_LOGOUT',		0x02);

	define('EVENT_M2W_CALL_BEGIN',	0x50);
	define('EVENT_M2W_CALL_END',	0x51);
	define('EVENT_RECORDED_PRES',	0x52);
	define('EVENT_RECORDED_MSG',	0x53);
	define('EVENT_RECORDED_BLOG',	0x54);
	define('EVENT_WARNED',			0x55);
	define('EVENT_PRES_APPROVED',	0x56);
	define('EVENT_PRES_DENIED',		0x57);

	$event_name[EVENT_USER_LOGIN] = 'User login';
	$event_name[EVENT_USER_LOGOUT] = 'User logout';

	$event_name[EVENT_M2W_CALL_BEGIN] = 'Call begin';
	$event_name[EVENT_M2W_CALL_END] = 'Call end';

	function addEvent($_type, $_category = 0 , $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_category) || !is_numeric($ownerId)) return false;

		$q = 'INSERT INTO tblEvents SET type='.$_type.', category='.$_category.', ownerId='.$ownerId.',timeCreated=NOW()';
		return $db->insert($q);
	}

	function getEvents($_category = 0, $ownerId = 0, $limit = '')
	{
		global $db;
		if (!is_numeric($_category) || !is_numeric($ownerId)) return false;

		$q = 'SELECT * FROM tblEvents';
		if ($_category) {
			$q .= ' WHERE category='.$_category;
			if ($ownerId) $q .= ' AND ownerId='.$ownerId;
		}
		$q .= ' ORDER BY timeCreated DESC'.$limit;

		return $db->getArray($q);
	}

	function getEventCnt($_category = 0, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_category) || !is_numeric($ownerId)) return false;

		$q = 'SELECT COUNT(*) FROM tblEvents';
		if ($_category) {
			$q .= ' WHERE category='.$_category;
			if ($ownerId) $q .= ' AND ownerId='.$ownerId;
		}

		return $db->getOneItem($q);
	}
?>
