<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	require_once('atom_settings.php');
	require_once('atom_feedback.php');	//for user abuse reporting feature

	require_once('functions_messages.php');	//for sendMessage()

	$config['user']['log_visitors'] = true;	//log each visit on users personal page from another user








	function getUserVisitors($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblVisits AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE ownerId='.$_id.' ORDER BY timeCreated DESC';
		return $db->getArray($q);
	}
?>