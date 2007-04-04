<?
	function searchLyrics($db, $query) {
		
		$query = addslashes(trim(strtolower($query)));

		$sql  = "SELECT t1.*, t2.bandName FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblBands AS t2 ON (t1.bandId=t2.bandId) ";
		$sql .= "WHERE LCASE(t1.lyricText) LIKE '%".$query."%' OR LCASE(t1.lyricName) LIKE '%".$query."%' ";
		$sql .= "ORDER BY t2.bandName ASC, t1.lyricName ASC";
		
		return dbArray($db, $sql);
	}

	/* Returns all lyrics with missing text */
	function getMissingLyrics($db)
	{
		$sql  = "SELECT tblLyrics.*, tblBands.bandName FROM tblLyrics ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblLyrics.bandId=tblBands.bandId) ";
		$sql .= "WHERE lyricText = '' ORDER BY tblBands.bandName ASC, tblLyrics.lyricName ASC";
		return dbArray($db, $sql);
	}

	/* Returns which records this lyric is on */
	function getLyricRecords($db, $lyric_id)
	{
		if (!is_numeric($lyric_id))
		{
			return false;
		}

		$sql  = "SELECT tblTracks.trackNumber, tblTracks.recordId, tblRecords.recordName, tblBands.bandId, tblBands.bandName FROM tblTracks ";
		$sql .= "LEFT OUTER JOIN tblRecords ON (tblTracks.recordId=tblRecords.recordId) ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblRecords.bandId=tblBands.bandId) ";
		$sql .= "WHERE lyricId = ".$lyric_id;

		return dbArray($db, $sql);
	}
	
	function lyricCount($db)
	{
		$check = dbQuery($db, "SELECT COUNT(lyricId) AS cnt FROM tblLyrics WHERE lyricText != ''");
		$data = dbFetchArray($check);
		return $data["cnt"];
	}

	function getLyricText($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;

		$check = dbQuery($db, "SELECT lyricText FROM tblLyrics WHERE lyricId=".$lyric_id);
		$data = dbFetchArray($check);
		return stripslashes($data["lyricText"]);
	}
	
	function getLyricData($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;

		$sql  = "SELECT t1.*,t2.userName,t3.bandName FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t1.bandId=t3.bandId) ";
		$sql .= "WHERE lyricId=".$lyric_id;

		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);
		if (!$row) return false;
		$row["lyricName"] = dbStripSlashes($row["lyricName"]);
		$row["lyricText"] = dbStripSlashes($row["lyricText"]);

		return $row;
	}	

	function getLyricName($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;

		$check = dbQuery($db, "SELECT lyricName FROM tblLyrics WHERE lyricId=".$lyric_id);
		$data = dbFetchArray($check);
		return stripslashes($data["lyricName"]);
	}

	function getIncompleteLyrics($db)
	{
		$sql  = "SELECT tblLyrics.*,tblBands.bandName FROM tblLyrics ";
		$sql .= "INNER JOIN tblBands ON (tblLyrics.bandId = tblBands.bandId) ";
		$sql .= "WHERE INSTR(lyricText, '???') ";
		$sql .= "ORDER BY tblBands.bandName ASC, tblLyrics.lyricName ASC";
		return dbArray($db, $sql);
	}

	function linkLyric($db, $record_id, $track, $lyric_id, $band_id)
	{
		if (!is_numeric($record_id) || !is_numeric($track) || !is_numeric($lyric_id) || !is_numeric($band_id))
		{
			return false;
		}
		
		dbQuery($db, "UPDATE tblTracks SET lyricId=".$lyric_id.",bandId=".$band_id." WHERE recordId=".$record_id." AND trackNumber=".$track);
		return true;
	}

	function updateLyric($db, $lyric_id, $lyric_name, $lyric_text)
	{
		if (!is_numeric($lyric_id)) return false;
		
		$lyric_name = cleanupText($lyric_name);
		$lyric_text = cleanupText($lyric_text);

		dbQuery($db, "UPDATE tblLyrics SET lyricName='".$lyric_name."', lyricText='".$lyric_text."' WHERE lyricId=".$lyric_id);
		return true;
	}
	
	function removeLyric($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;
		
		dbQuery($db, "DELETE FROM tblLyrics WHERE lyricId=".$lyric_id);
		dbQuery($db, "UPDATE tblTracks SET lyricId=0,bandId=0 WHERE lyricId=".$lyric_id);
		
	}

	function addLyric($db, $user_id, $band_id, $record_id, $track, $lyric_name, $lyric_text)
	{
		if (!is_numeric($user_id) || !is_numeric($band_id) || !is_numeric($record_id) || !is_numeric($track)) return false;

		$lyric_name = cleanupText($lyric_name);
		$lyric_text = cleanupText($lyric_text);

		dbQuery($db, 'INSERT INTO tblLyrics SET bandId='.$band_id.',lyricName="'.$lyric_name.'",lyricText="'.$lyric_text.'",creatorId='.$user_id.',timestamp='.time());
		$lyric_id = $db['insert_id'];

		if ($record_id) {
			dbQuery($db, 'UPDATE tblTracks SET lyricId='.$lyric_id.',bandId='.$band_id.' WHERE recordId='.$record_id.' AND trackNumber='.$track);
		}
		return $lyric_id;
	}
	
	function getLyricsThatBandCovers($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t1.recordId,t1.trackNumber,t1.lyricId,t2.lyricName,t2.bandId,t3.bandName,t4.recordName ";
		$sql .= "FROM tblTracks AS t1 ";
		$sql .= "INNER JOIN tblLyrics AS t2 ON (t1.lyricId=t2.lyricId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t2.bandId=t3.bandId) ";
		$sql .= "INNER JOIN tblRecords AS t4 ON (t1.recordId=t4.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t1.bandId!=t2.bandId ";
		$sql .= "GROUP BY t1.lyricId ";
		$sql .= "ORDER BY t3.bandName ASC,t4.recordName ASC,t1.trackNumber ASC";
		
		return dbArray($db, $sql);		
	}
	
	function getLyricsThatOtherCovers($db, $band_id)
	{
		if (!is_numeric($band_id)) return false;
		
		$sql  = "SELECT t1.lyricId,t1.lyricName,t2.recordId,t2.bandId,t2.trackNumber,t3.bandName,t4.recordName ";
		$sql .= "FROM tblLyrics AS t1 ";
		$sql .= "INNER JOIN tblTracks AS t2 ON (t1.lyricId=t2.lyricId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t2.bandId=t3.bandId) ";
		$sql .= "INNER JOIN tblRecords AS t4 ON (t2.recordId=t4.recordId) ";
		$sql .= "WHERE t1.bandId=".$band_id." AND t2.bandId!=".$band_id." ";
		$sql .= "ORDER BY t3.bandName ASC,t4.recordName ASC,t2.trackNumber ASC";
		
		return dbArray($db, $sql);		
	}

?>