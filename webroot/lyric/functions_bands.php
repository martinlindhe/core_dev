<?
	/* functions_bands.php */
	
	function addBand($creator_id, $band_name)
	{
		global $db;

		if (!is_numeric($creator_id)) return false;
		
		$band_name = $db->escape(trim($band_name));

		$check = $db->getOneRow("SELECT * FROM tblBands WHERE bandName='".$band_name."'");
		if ($check) return false; //a band with this name already exists

		$sql = "INSERT INTO tblBands SET bandName='".$band_name."',creatorId=".$creator_id.",timestamp=".time();
		$db->query($sql);
		return $db->insert_id;
	}
	
	function updateBandInfo($band_id, $band_info)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		$band_info = $db->escape($band_info);

		$db->query('UPDATE tblBands SET bandInfo="'.$band_info.'" WHERE bandId='.$band_id);
		return true;
	}
	
	function getBands()
	{
		global $db;

		return $db->getArray('SELECT * FROM tblBands ORDER BY bandName ASC');
	}

	function getBandName($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		return $db->getOneItem('SELECT bandName FROM tblBands WHERE bandId='.$band_id);
	}

	function getBandInfo($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		$sql  = "SELECT t1.*,t2.userName FROM tblBands AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$sql .= "WHERE t1.bandId=".$band_id;

		return $db->getOneRow($sql);
	}	
	
	function bandCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(bandId) FROM tblBands');
	}

	function getBandRecordCount($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		return $db->getOneItem('SELECT COUNT(recordId) FROM tblRecords WHERE bandId='.$band_id);
	}
	
	/* Returns the records that this band has made */
	function getBandRecords($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		return $db->getArray('SELECT * FROM tblRecords WHERE bandId='.$band_id.' ORDER BY recordName ASC');
	}
	
	function getBandCompilations($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t2.recordId,t2.recordName FROM tblTracks AS t1 ";
		$sql .= "INNER JOIN tblRecords AS t2 ON (t1.recordId=t2.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t2.bandId=0 ";
		$sql .= "GROUP BY t2.recordId ";
		$sql .= "ORDER BY t2.recordName ASC";

		return $db->getArray($sql);
	}
	
	/* Returnerar låttitlar och id till alla låtar med detta band i alfabetisk ordning */
	function getBandLyrics($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		$sql  = 'SELECT lyricId,lyricName FROM tblLyrics WHERE bandId='.$band_id;
		$sql .= ' ORDER BY lyricName ASC';

		return $db->getArray($sql);
	}

?>