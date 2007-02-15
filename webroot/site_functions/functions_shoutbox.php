<?
	function GetLastShouts(&$db, $ammount=5)
	{
		if (!is_numeric($ammount)) $ammount=5;

		$sql  = 'SELECT t1.*,t2.userName FROM tblShouts AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'ORDER BY t1.timestamp DESC ';
		$sql .= 'LIMIT '.$ammount;

		return dbArray($db, $sql);
	}

	function AddShout(&$db, $userId, $msg)
	{
		if (!is_numeric($userId)) return false;

		$msg = dbAddSlashes($db, strip_tags($msg));
		if (!$msg) return false;
		
		dbQuery($db, 'INSERT INTO tblShouts SET userId='.$userId.',msg="'.$msg.'",timestamp='.time() );
	}
?>