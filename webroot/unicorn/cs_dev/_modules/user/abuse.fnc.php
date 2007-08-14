<?
	/* abuse functions, by martin lindhe 2007.05.29 */
	
	function abuseReport($_id, $_msg)
	{
		global $sql, $l;

		if (!is_numeric($_id)) return false;
		
		$q = 'INSERT INTO s_userabuse SET reporterId='.$l['id_id'].',reportedId='.$_id.',msg="'.secureINS($_msg).'",timeReported=NOW()';
		$sql->queryInsert($q);
	}
?>