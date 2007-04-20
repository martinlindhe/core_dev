<?
	/* functions_records.php */
	
	function cleanupText($text)
	{
		global $db;

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

		$text = $db->escape($text);

		return $text;		
	}

	function addRecord($band_id, $record_name, $record_info)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($band_id)) return false;

		$record_name = $db->escape(trim($record_name));
		$record_info = $db->escape(trim($record_info));

		$db->query('INSERT INTO tblRecords SET recordName="'.$record_name.'",bandId='.$band_id.',recordInfo="'.$record_info.'",creatorId='.$session->id.',timeCreated=NOW()');

		return $db->insert_id;
	}

	function createTracks($record_id, $tracks)
	{
		global $db;

		if (!is_numeric($record_id) || !is_numeric($tracks)) return false;
		
		for ($i=1; $i<=$tracks; $i++)
		{
			$db->query("INSERT INTO tblTracks SET recordId=".$record_id.",trackNumber=".$i.",lyricId=0");
		}
	}
	
	function getBandIdFromRecordId($record_id)
	{
		global $db;

		if (!is_numeric($record_id)) return false;
		
		return $db->getOneItem('SELECT bandId FROM tblRecords WHERE recordId='.$record_id);
	}
	
	
	function getRecordName($record_id)
	{
		global $db;

		if (!is_numeric($record_id)) return false;

		$name = $db->getOneItem('SELECT recordName FROM tblRecords WHERE recordId='.$record_id);
		$name = stripslashes($name);
		if (!$name) return 's/t';
		return $name;
	}	
	
	function getRecordTracks($record_id)
	{
		global $db;

		if (!is_numeric($record_id)) return false;
		
		$q  = "SELECT tblTracks.*, tblLyrics.lyricName, tblLyrics.lyricText, tblLyrics.bandId AS authorId, tblBands.bandName FROM tblTracks ";
		$q .= "LEFT OUTER JOIN tblLyrics ON (tblTracks.lyricId=tblLyrics.lyricId) ";
		$q .= "LEFT OUTER JOIN tblBands ON (tblTracks.bandId=tblBands.bandId) ";
		$q .= "WHERE tblTracks.recordId = ".$record_id." ";
		$q .= "ORDER BY tblTracks.trackNumber ASC";

		return $db->getArray($q);
	}

	function getRecordTrackCount($record_id)
	{
		global $db;
		if (!is_numeric($record_id)) return false;

		return $db->getOneItem('SELECT COUNT(trackNumber) FROM tblTracks WHERE recordId='.$record_id);
	}

	function updateRecord($record_id, $record_name)
	{
		global $db;

		if (!is_numeric($record_id)) return false;

		$record_name = cleanupText($record_name);

		$db->query("UPDATE tblRecords SET recordName='".$record_name."' WHERE recordId=".$record_id);
		return true;
	}

	function recordCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(recordId) FROM tblRecords');
	}

	function trackCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(recordId) FROM tblTracks');
	}

	function clearTrack($record_id, $track)
	{
		global $db;

		$q = "UPDATE tblTracks SET lyricId=0,bandId=0 WHERE recordId=".$record_id." AND trackNumber=".$track;
		$db->query($q);
	}

	/* Adds a track to the end of recordId */
	function addTrack($record_id)
	{
		global $db;

		if (!is_numeric($record_id)) return false;

		$q  = "SELECT MAX(trackNumber) AS num,bandId FROM tblTracks ";
		$q .= "WHERE recordId=".$record_id." ";
		$q .= "GROUP BY bandId";
		$row = $db->getOneRow($q);

		$q = "INSERT INTO tblTracks SET trackNumber=".($row["num"]+1).",recordId=".$record_id.",bandId=".$row["bandId"];
		$db->query($q);
	}

	function removeTrack($record_id, $track)
	{
		global $db;

		$q = "DELETE FROM tblTracks WHERE recordId=".$record_id." AND trackNumber=".$track;
		$db->query($q);
	}

	function getLyricBandName($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		$q = "SELECT bandName FROM tblLyrics ";
		$q .= "LEFT OUTER JOIN tblBands ON (tblLyrics.bandId=tblBands.bandId) ";
		$q .= "WHERE lyricId=".$lyric_id;

		$data = $db->getOneItem($q);
		return stripslashes($data);
	}

	function getLyricBandId($lyric_id)
	{
		global $db;

		if (!is_numeric($lyric_id)) return false;

		return $db->getOneItem('SELECT bandId FROM tblLyrics WHERE lyricId='.$lyric_id);
	}
	
	/* Returns the recordInfo field on recordId */
	function getRecordInfo($record_id)
	{
		global $db;
		
		if (!is_numeric($record_id)) return false;

		$info = $db->getOneItem('SELECT recordInfo FROM tblRecords WHERE recordId='.$record_id);

		return stripslashes($info);
	}
	
	function getRecordData($record_id)
	{	
		global $db;

		if (!is_numeric($record_id)) return false;
		
		$q  = "SELECT t1.*,t2.userName,t3.bandName FROM tblRecords AS t1 ";
		$q .= "INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ";
		$q .= "INNER JOIN tblBands AS t3 ON (t1.bandId=t3.bandId) ";
		$q .= "WHERE t1.recordId=".$record_id;
		
		return $db->getOneItem($q);
	}	
	
	/* Changes the band who created this record + all its associated lyrics to $band_id */
	function changeRecordOwner($record_id, $band_id)
	{
		global $db;

		$q = "UPDATE tblTracks SET bandId=".$band_id." WHERE recordId=".$record_id;
		$db->query($q);
		
		$q = "UPDATE tblRecords SET bandId=".$band_id." WHERE recordId=".$record_id;
		$db->query($q);
		
		$q = "SELECT lyricId FROM tblTracks WHERE recordId=".$record_id;
		$list = $db->getArray($q);

		for ($i=0; $i<count($list); $i++) {
			$q = "UPDATE tblLyrics SET bandId=".$band_id." WHERE lyricId=".$list[$i]["lyricId"];
			$db->query($q);
		}
	}
?>