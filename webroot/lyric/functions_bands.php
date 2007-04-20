<?
	/* functions_bands.php */
	
	function addBand($band_name)
	{
		global $db, $session;

		if (!$session->id) return false;

		$band_name = $db->escape(trim($band_name));

		$check = $db->getOneRow("SELECT * FROM tblBands WHERE bandName='".$band_name."'");
		if ($check) return false; //a band with this name already exists

		$q = "INSERT INTO tblBands SET bandName='".$band_name."',creatorId=".$session->id.",timeCreated=NOW()";
		$db->query($q);
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

	function getBandName($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		return $db->getOneItem('SELECT bandName FROM tblBands WHERE bandId='.$_id);
	}

	function setBandName($_id, $_name)
	{
		global $db;
		
		if (!is_numeric($_id)) return false;

		$db->query('UPDATE tblBands SET bandName="'.$db->escape($_name).'" WHERE bandId='.$_id);
	}

	function getBandInfo($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		$q  = "SELECT t1.*,t2.userName FROM tblBands AS t1 ";
		$q .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$q .= "WHERE t1.bandId=".$band_id;

		return $db->getOneRow($q);
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
		
		$q  = "SELECT t2.recordId,t2.recordName FROM tblTracks AS t1 ";
		$q .= "INNER JOIN tblRecords AS t2 ON (t1.recordId=t2.recordId) ";
		$q .= "WHERE t1.bandId=".$band_id." AND t2.bandId=0 ";
		$q .= "GROUP BY t2.recordId ";
		$q .= "ORDER BY t2.recordName ASC";

		return $db->getArray($q);
	}
	
	/* Returnerar låttitlar och id till alla låtar med detta band i alfabetisk ordning */
	function getBandLyrics($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;

		$q  = 'SELECT lyricId,lyricName FROM tblLyrics WHERE bandId='.$band_id;
		$q .= ' ORDER BY lyricName ASC';

		return $db->getArray($q);
	}

?>