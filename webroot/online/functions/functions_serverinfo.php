<?
	/* functions_serverinfo.php */

	function addServerDowntime($db, $date, $info) {
		$timestamp = strtotime($date);
		if (!$timestamp) return false;
		$info = addslashes($info);
		
		dbQuery($db, "INSERT INTO tblServerDowntimes SET info='".$info."',timestamp=".$timestamp);
		return true;
	}
	
	function getServerDowntimes($db) {
		$sql = "SELECT * FROM tblServerDowntimes WHERE timestamp>".time()." ORDER BY timestamp ASC";
		return dbArray($db, $sql);
	}
	
	/* Returns a infotext if the server is currently down, or false if everything should be up and running */
	function getCurrentServerDowntime($db) {
		
		$month = date("n");
		$day   = date("j");
		$year  = date("Y");
		$current_midnight = mktime(0, 0, 0, $month, $day, $year);
		$next_midnight    = mktime(0, 0, 0, $month, $day+1, $year);
		
		$sql = "SELECT info FROM tblServerDowntimes WHERE timestamp>=".$current_midnight." AND timestamp<=".$next_midnight;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {
			$row = dbFetchArray($check);
			return $row["info"];
		} else {
			return false;
		}
	}
	
?>