<?
	/* functions_bands.php */
	
	function addBand($db, $creator_id, $band_name)
	{
		if (!is_numeric($creator_id)) return false;
		
		$band_name = dbAddSlashes($db, trim($band_name));
		
		$check = dbQuery($db, "SELECT * FROM tblBands WHERE bandName='".$band_name."'");
		if (dbNumRows($check)) return false; //a band with this name already exists

		$sql = "INSERT INTO tblBands SET bandName='".$band_name."',creatorId=".$creator_id.",timestamp=".time();
		dbQuery($db, $sql);
		return mysql_insert_id(); //return id
	}
	
	function updateBandInfo($db, $band_id, $band_info)
	{
		if (!is_numeric($band_id)) return false;

		$band_info = addslashes($band_info);
		
		dbQuery($db, "UPDATE tblBands SET bandInfo='".$band_info."' WHERE bandId=".$band_id);
		return true;
	}
	
	
	function getBands($db)
	{
		return dbArray($db, "SELECT * FROM tblBands ORDER BY bandName ASC");
	}

	function getBandName($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;

		$check = dbQuery($db, "SELECT bandName FROM tblBands WHERE bandId=".$band_id);
		$data = dbFetchArray($check);
		return stripslashes($data["bandName"]);
	}

	function getBandInfo($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;

		$sql  = "SELECT t1.*,t2.userName FROM tblBands AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$sql .= "WHERE t1.bandId=".$band_id;

		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}	
	
	function bandCount($db)
	{
		return dbOneResultItem($db, "SELECT COUNT(bandId) FROM tblBands");
	}

	function getBandRecordCount($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;

		return dbOneResultItem($db, "SELECT COUNT(recordId) FROM tblRecords WHERE bandId=".$band_id);
	}
	
	/* Returns the records that this band has made */
	function getBandRecords($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;

		return dbArray($db, "SELECT * FROM tblRecords WHERE bandId=".$band_id." ORDER BY recordName ASC");
	}
	
	function getBandCompilations($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t2.recordId,t2.recordName FROM tblTracks AS t1 ";
		$sql .= "INNER JOIN tblRecords AS t2 ON (t1.recordId=t2.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t2.bandId=0 ";
		$sql .= "GROUP BY t2.recordId ";
		$sql .= "ORDER BY t2.recordName ASC";

		return dbArray($db, $sql);
	}
	
	/* Returnerar låttitlar och id till alla låtar med detta band i alfabetisk ordning */
	function getBandLyrics($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;

		$sql  = "SELECT lyricId,lyricName FROM tblLyrics WHERE bandId=".$band_id." ";
		$sql .= "ORDER BY lyricName ASC";
		
		return dbArray($db, $sql);
	}

?>