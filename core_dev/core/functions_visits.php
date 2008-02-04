<?
/**
 * $Id$
 *
 * Simple functions to enable visit logs for various objects
 */

	define('VISIT_USERPAGE',	1);
	define('VISIT_USERIMAGE',	2);

	function logVisit($_type, $_owner)
	{
		global $db, $session;
		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q = 'INSERT INTO tblVisits SET ownerId='.$_owner.',creatorId='.$session->id.',timeCreated=NOW()';
		$db->insert($q);
	}

	function getVisits($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblVisits AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE ownerId='.$_id.' AND type='.$_type.' ORDER BY timeCreated DESC';
		return $db->getArray($q);
	}
?>