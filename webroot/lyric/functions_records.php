<?
	/* functions_records.php */
	
	function cleanupText($text) {

		$text = trim($text);
		
		do { /* Remove chunks of whitespace */
			$temp = $text;
			$text = str_replace("  ", " ", $text);
		} while ($text != $temp);
		
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace(" \n", "\n", $text);
		$text = str_replace("\n ", "\n", $text);

		$text = str_replace("`", "'", $text);
		$text = str_replace("’", "'", $text);
		$text = str_replace("´", "'", $text);
		$text = str_replace("…", "...", $text);
		
		$text = str_replace("“", "\"", $text);
		$text = str_replace("”", "\"", $text);
		$text = str_replace("‘", "\"", $text);
		
		/* Clean up common miswriting */
		$text = str_replace(" youve ", " you\'ve ", $text);
		$text = str_replace(" youre ", " you\'re ", $text);
		$text = str_replace(" yourll ", " you\'ll ", $text);
		$text = str_replace(" theyre ", " they\'re ", $text);
		$text = str_replace(" i\'m ", " I\'m ", $text);

		$text = addslashes($text);

		return $text;		
	}
	
	
	function addRecord($db, $creator_id, $band_id, $record_name, $record_info)
	{
		if (!is_numeric($band_id) || !is_numeric($creator_id))
		{
			return false;
		}
		
		$record_name = trim($record_name);
		$record_name = addslashes($record_name);
		
		$record_info = trim($record_info);
		$record_info = addslashes($record_info);
		
		dbQuery($db, "INSERT INTO tblRecords SET recordName='".$record_name."',bandId=".$band_id.",recordInfo='".$record_info."',creatorId=".$creator_id.",timestamp=".time());

		return $db['insert_id'];
	}
	
	function createTracks($db, $record_id, $tracks)
	{
		if (!is_numeric($record_id) || !is_numeric($tracks))
		{
			return false;
		}
		
		for ($i=1; $i<=$tracks; $i++)
		{
			
			dbQuery($db, "INSERT INTO tblTracks SET recordId=".$record_id.",trackNumber=".$i.",lyricId=0");
			
		}
	}
	
	function getBandIdFromRecordId($db, $record_id)
	{
		if (!is_numeric($record_id))
		{
			return false;
		}
		
		$check = dbQuery($db, "SELECT bandId FROM tblRecords WHERE recordId=".$record_id);
		$data = dbFetchArray($check);
		return $data["bandId"];
	}
	
	
	function getRecordName($db, $record_id)
	{
		if (!is_numeric($record_id))
		{
			return false;
		}

		$check = dbQuery($db, "SELECT recordName FROM tblRecords WHERE recordId=".$record_id);
		$data = dbFetchArray($check);
		$name = stripslashes($data["recordName"]);
		if (!$name)
		{
			$name = "s/t";
		}
		return $name;
	}	
	
	function getRecordTracks($db, $record_id)
	{
		if (!is_numeric($record_id))
		{
			return false;
		}
		
		$sql  = "SELECT tblTracks.*, tblLyrics.lyricName, tblLyrics.lyricText, tblLyrics.bandId AS authorId, tblBands.bandName FROM tblTracks ";
		$sql .= "LEFT OUTER JOIN tblLyrics ON (tblTracks.lyricId=tblLyrics.lyricId) ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblTracks.bandId=tblBands.bandId) ";
		$sql .= "WHERE tblTracks.recordId = ".$record_id." ";
		$sql .= "ORDER BY tblTracks.trackNumber ASC";

		return dbArray($db, $sql);
	}

	function getRecordTrackCount($db, $record_id)
	{
		if (!is_numeric($record_id))
		{
			return false;
		}

		$sql = "SELECT COUNT(trackNumber) AS cnt FROM tblTracks WHERE recordId=".$record_id;
		$check = dbQuery($db, $sql);
		$data = dbFetchArray($check);

		return $data["cnt"];
	}

	function updateRecord($db, $record_id, $record_name)
	{
		if (!is_numeric($record_id))
		{
			return false;
		}

		$record_name = cleanupText($record_name);

		dbQuery($db, "UPDATE tblRecords SET recordName='".$record_name."' WHERE recordId=".$record_id);
		return true;
	}

	function recordCount($db)
	{
		$check = dbQuery($db, "SELECT COUNT(recordId) AS cnt FROM tblRecords");
		$data = dbFetchArray($check);
		return $data["cnt"];
	}

	function trackCount($db)
	{
		$check = dbQuery($db, "SELECT COUNT(recordId) AS cnt FROM tblTracks");
		$data = dbFetchArray($check);
		return $data["cnt"];
	}

	function clearTrack($db, $record_id, $track)
	{
		$sql = "UPDATE tblTracks SET lyricId=0,bandId=0 WHERE recordId=".$record_id." AND trackNumber=".$track;
		dbQuery($db, $sql);
	}

	/* Adds a track to the end of recordId */
	function addTrack($db, $record_id) {

		if (!is_numeric($record_id)) return false;

		$sql  = "SELECT MAX(trackNumber) AS num,bandId FROM tblTracks ";
		$sql .= "WHERE recordId=".$record_id." ";
		$sql .= "GROUP BY bandId";
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		$sql = "INSERT INTO tblTracks SET trackNumber=".($row["num"]+1).",recordId=".$record_id.",bandId=".$row["bandId"];
		dbQuery($db, $sql);
	}

	function removeTrack($db, $record_id, $track)
	{
		$sql = "DELETE FROM tblTracks WHERE recordId=".$record_id." AND trackNumber=".$track;
		dbQuery($db, $sql);
	}

	function getLyricBandName($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;

		$sql = "SELECT bandName FROM tblLyrics ";
		$sql .= "LEFT OUTER JOIN tblBands ON (tblLyrics.bandId=tblBands.bandId) ";
		$sql .= "WHERE lyricId=".$lyric_id;
		
		$check = dbQuery($db, $sql);
		$data = dbFetchArray($check);
		return stripslashes($data["bandName"]);
	}

	function getLyricBandId($db, $lyric_id)
	{
		if (!is_numeric($lyric_id)) return false;

		$sql = "SELECT bandId FROM tblLyrics WHERE lyricId=".$lyric_id;

		$check = dbQuery($db, $sql);
		$data = dbFetchArray($check);
		return stripslashes($data["bandId"]);
	}
	
	/* Returns the recordInfo field on recordId */
	function getRecordInfo($db, $record_id) {
		
		if (!is_numeric($record_id)) return false;
		
		$sql = "SELECT recordInfo FROM tblRecords WHERE recordId=".$record_id;
		
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);
		
		return stripslashes($row["recordInfo"]);
	}
	
	function getRecordData($db, $record_id) {
		
		if (!is_numeric($record_id)) return false;
		
		$sql  = "SELECT t1.*,t2.userName,t3.bandName FROM tblRecords AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$sql .= "INNER JOIN tblBands AS t3 ON (t1.bandId=t3.bandId) ";
		$sql .= "WHERE t1.recordId=".$record_id;
		
		$check = dbQuery($db, $sql);

		return dbFetchArray($check);
	}	
	
	/* Changes the band who created this record + all its associated lyrics to $band_id */
	function changeRecordOwner($db, $record_id, $band_id) {
		
		$sql = "UPDATE tblTracks SET bandId=".$band_id." WHERE recordId=".$record_id;
		dbQuery($db, $sql);
		
		$sql = "UPDATE tblRecords SET bandId=".$band_id." WHERE recordId=".$record_id;
		dbQuery($db, $sql);
		
		$sql = "SELECT lyricId FROM tblTracks WHERE recordId=".$record_id;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {
			$sql = "UPDATE tblLyrics SET bandId=".$band_id." WHERE lyricId=".$list[$i]["lyricId"];
			dbQuery($db, $sql);
		}
		
	}
?>