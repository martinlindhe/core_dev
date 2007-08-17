<?
	/* abuse functions, by martin lindhe 2007.05.29 */
	
	function abuseReport($_id, $_msg)
	{
		global $db, $user;
		if (!is_numeric($_id)) return false;

		$q = 'INSERT INTO s_userabuse SET reporterId='.$user->id.',reportedId='.$_id.',msg="'.$db->escape($_msg).'",timeReported=NOW()';
		$db->insert($q);
	}
?>