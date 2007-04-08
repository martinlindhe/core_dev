<?
	function searchLyrics($query)
	{
		global $db;

		$query = $db->escape(trim(strtolower($query)));

		$sql  = "SELECT t1.*, t2.bandName FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblBands AS t2 ON (t1.bandId=t2.bandId) ";
		$sql .= "WHERE LCASE(t1.lyricText) LIKE '%".$query."%' OR LCASE(t1.lyricName) LIKE '%".$query."%' ";
		$sql .= "ORDER BY t2.bandName ASC, t1.lyricName ASC";
		
		return $db->getArray($sql);
	}

	/* Returns all lyrics with missing text */
	function getMissingLyrics()
	{
		global $db;

		$sql  = "SELECT tblLyrics.*, tblBands.bandName FROM tblLyrics ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblLyrics.bandId=tblBands.bandId) ";
		$sql .= "WHERE lyricText = '' ORDER BY tblBands.bandName ASC, tblLyrics.lyricName ASC";
		return $db->getArray($sql);
	}

	/* Returns which records this lyric is on */
	function getLyricRecords($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$sql  = "SELECT tblTracks.trackNumber, tblTracks.recordId, tblRecords.recordName, tblBands.bandId, tblBands.bandName FROM tblTracks ";
		$sql .= "LEFT OUTER JOIN tblRecords ON (tblTracks.recordId=tblRecords.recordId) ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblRecords.bandId=tblBands.bandId) ";
		$sql .= "WHERE lyricId = ".$lyric_id;

		return $db->getArray($sql);
	}
	
	function lyricCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(lyricId) FROM tblLyrics WHERE lyricText != ""');
	}

	function getLyricText($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$data = $db->getOneItem("SELECT lyricText FROM tblLyrics WHERE lyricId=".$lyric_id);
		return stripslashes($data);
	}

	function getLyricData($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$sql  = "SELECT t1.*,t2.userName,t3.bandName FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t1.bandId=t3.bandId) ";
		$sql .= "WHERE lyricId=".$lyric_id;

		$row = $db->getOneRow($sql);
		if (!$row) return false;

		$row['lyricName'] = stripslashes($row['lyricName']);
		$row['lyricText'] = stripslashes($row['lyricText']);

		return $row;
	}	

	function getLyricName($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$data = $db->getOneItem('SELECT lyricName FROM tblLyrics WHERE lyricId='.$lyric_id);
		return stripslashes($data);
	}

	function getIncompleteLyrics()
	{
		global $db;

		$sql  = "SELECT tblLyrics.*,tblBands.bandName FROM tblLyrics ";
		$sql .= "INNER JOIN tblBands ON (tblLyrics.bandId = tblBands.bandId) ";
		$sql .= "WHERE INSTR(lyricText, '???') ";
		$sql .= "ORDER BY tblBands.bandName ASC, tblLyrics.lyricName ASC";
		return $db->getArray($sql);
	}

	function linkLyric($record_id, $track, $lyric_id, $band_id)
	{
		global $db;

		if (!is_numeric($record_id) || !is_numeric($track) || !is_numeric($lyric_id) || !is_numeric($band_id)) return false;
		
		$db->query("UPDATE tblTracks SET lyricId=".$lyric_id.",bandId=".$band_id." WHERE recordId=".$record_id." AND trackNumber=".$track);
		return true;
	}

	function updateLyric($lyric_id, $lyric_name, $lyric_text)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;
		
		$lyric_name = cleanupText($lyric_name);
		$lyric_text = cleanupText($lyric_text);

		$db->query('UPDATE tblLyrics SET lyricName="'.$lyric_name.'", lyricText="'.$lyric_text.'" WHERE lyricId='.$lyric_id);
		return true;
	}
	
	function removeLyric($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$db->query('DELETE FROM tblLyrics WHERE lyricId='.$lyric_id);
		$db->query('UPDATE tblTracks SET lyricId=0,bandId=0 WHERE lyricId='.$lyric_id);
	}

	function addLyric($band_id, $record_id, $track, $lyric_name, $lyric_text)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($band_id) || !is_numeric($record_id) || !is_numeric($track)) return false;

		$lyric_name = cleanupText($lyric_name);
		$lyric_text = cleanupText($lyric_text);

		$db->query('INSERT INTO tblLyrics SET bandId='.$band_id.',lyricName="'.$lyric_name.'",lyricText="'.$lyric_text.'",creatorId='.$session->id.',timestamp='.time());
		$lyric_id = $db->insert_id;

		if ($record_id) {
			$db->query('UPDATE tblTracks SET lyricId='.$lyric_id.',bandId='.$band_id.' WHERE recordId='.$record_id.' AND trackNumber='.$track);
		}
		return $lyric_id;
	}
	
	function getLyricsThatBandCovers($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t1.recordId,t1.trackNumber,t1.lyricId,t2.lyricName,t2.bandId,t3.bandName,t4.recordName ";
		$sql .= "FROM tblTracks AS t1 ";
		$sql .= "INNER JOIN tblLyrics AS t2 ON (t1.lyricId=t2.lyricId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t2.bandId=t3.bandId) ";
		$sql .= "INNER JOIN tblRecords AS t4 ON (t1.recordId=t4.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t1.bandId!=t2.bandId ";
		$sql .= "GROUP BY t1.lyricId ";
		$sql .= "ORDER BY t3.bandName ASC,t4.recordName ASC,t1.trackNumber ASC";
		
		return $db->getArray($sql);		
	}
	
	function getLyricsThatOtherCovers($band_id)
	{
		global $db;

		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t1.lyricId,t1.lyricName,t2.recordId,t2.bandId,t2.trackNumber,t3.bandName,t4.recordName ";
		$sql .= "FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblTracks AS t2 ON (t1.lyricId=t2.lyricId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t2.bandId=t3.bandId) ";
		$sql .= "INNER JOIN tblRecords AS t4 ON (t2.recordId=t4.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t2.bandId!=".$band_id." ";
		$sql .= "ORDER BY t3.bandName ASC,t4.recordName ASC,t2.trackNumber ASC";
		
		return $db->getArray($sql);		
	}
?>