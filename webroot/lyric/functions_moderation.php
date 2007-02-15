<?
	define("MODERATION_BAND",	1);
	define("MODERATION_RECORD",	2);
	define("MODERATION_LYRIC",	3);
	
	define("MODERATIONCHANGE_LYRICLINK",	1);
	define("MODERATIONCHANGE_LYRIC",		2);
	define("MODERATIONCHANGE_RECORDNAME",	3);


	//lägg till ett nyskapat objekt i modereringskön
	function addModerationItem($db, $id, $type)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;
		
		dbQuery($db, "INSERT INTO tblNewAdditions SET ID=".$id.",type=".$type.",timestamp=".time());
	}
	
	function isModerated($db, $id, $type)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;

		$check = dbQuery($db, "SELECT ID FROM tblNewAdditions WHERE ID=".$id." AND type=".$type);
		if (dbNumRows($check)) {
			return true;
		}
		return false;
	}
	
	function countNewAdditions($db)
	{
		return dbOneResultItem($db, "SELECT COUNT(*) FROM tblNewAdditions");
	}
	
	/* Returns all new additions that needs to be moderated */
	function getNewAdditions($db)
	{	//älst först
		return dbArray($db, "SELECT * FROM tblNewAdditions ORDER BY timestamp ASC");
	}
	
	function acceptNewAddition($db, $type, $id)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;

		removeNewAddition($db, $type, $id);
	}

	function denyNewAddition($db, $type, $id)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;

		removeNewAddition($db, $type, $id);
		switch ($type) {
			case MODERATION_BAND:
				dbQuery($db, "DELETE FROM tblBands WHERE bandId=".$id);
				dbQuery($db, "DELETE FROM tblRecords WHERE bandId=".$id);
				dbQuery($db, "DELETE FROM tblTracks WHERE bandId=".$id);
				dbQuery($db, "DELETE FROM tblLyrics WHERE bandId=".$id);
				break;
				
			case MODERATION_RECORD:
				dbQuery($db, "DELETE FROM tblRecords WHERE recordId=".$id);
				dbQuery($db, "DELETE FROM tblTracks WHERE recordId=".$id);
				break;
				
			case MODERATION_LYRIC:
				dbQuery($db, "DELETE FROM tblLyrics WHERE lyricId=".$id);
				break;
		}
	}
	
	function removeNewAddition($db, $type, $id)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;
		
		dbQuery($db, "DELETE FROM tblNewAdditions WHERE ID=".$id." AND type=".$type);
	}
	
	
	
	//--- modify existing data-functions:
	
	
	function addPendingChange($db, $type, $p1, $p2, $p3="")
	{
		if (!is_numeric($type)) return false;
		
		if ($type == MODERATIONCHANGE_LYRICLINK) {	//db, type, record_id, track_id
			if (!is_numeric($p1) || !is_numeric($p2)) return false;
			dbQuery($db, "INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',timestamp=".time());
			
		} else if ($type == MODERATIONCHANGE_LYRIC) { //db, type, lyric_id, title, text
			if (!is_numeric($p1)) return false;
			
			$p2 = dbAddSlashes($db, $p2);
			$p3 = dbAddSlashes($db, $p3);
			dbQuery($db, "INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',p3='".$p3."',timestamp=".time());
			
		} else if ($type == MODERATIONCHANGE_RECORDNAME) { //db, type, record_id, title
			if (!is_numeric($p1)) return false;

			$p2 = dbAddSlashes($p2);
			dbQuery($db, "INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',timestamp=".time());

		} else {
			echo "addPendingChange(): unknown TYPE: ".$type;
			die;
		}
	}
	
	function countPendingChanges($db)
	{
		return dbOneResultItem($db, "SELECT COUNT(*) FROM tblPendingChanges");
	}
	
	function isPendingChange($db, $type, $p1, $p2="")
	{
		if ($type == MODERATIONCHANGE_LYRICLINK) {
			if (!is_numeric($p1) || !is_numeric($p2)) return false;
			$check = dbQuery($db, "SELECT type FROM tblPendingChanges WHERE p1=".$p1." AND p2=".$p2);
			if (dbNumRows($check)) return true;
		} else if ($type == MODERATIONCHANGE_LYRIC) {
			if (!is_numeric($p1)) return false;
			$check = dbQuery($db, "SELECT type FROM tblPendingChanges WHERE p1=".$p1);
			if (dbNumRows($check)) return true;

		} else if ($type == MODERATIONCHANGE_RECORDNAME) {
			if (!is_numeric($p1)) return false;
			$check = dbQuery($db, "SELECT type FROM tblPendingChanges WHERE p1=".$p1);
			if (dbNumRows($check)) return true;

		} else {
			echo "addPendingChange(): unknown TYPE: ".$type;
			die;
		}

		return false;
	}
	
	function getPendingChanges($db)
	{//returnera älst först
		return dbArray($db, "SELECT * FROM tblPendingChanges ORDER BY timestamp ASC");
	}
	
	function denyPendingChange($db, $type, $p1)
	{
		if (!is_numeric($type) || !is_numeric($p1)) return false;
		
		dbQuery($db, "DELETE FROM tblPendingChanges WHERE type=".$type." AND p1=".$p1);
	}
	
	function getPendingChange($db, $type, $p1)
	{
		if (!is_numeric($type) || !is_numeric($p1)) return false;
		
		$check = dbQuery($db, "SELECT * FROM tblPendingChanges WHERE type=".$type." AND p1='".$p1."'");
		return dbFetchArray($check);
	}
	
	function acceptPendingChange($db, $type, $p1)
	{
		if (!is_numeric($type) || !is_numeric($p1)) return false;
		
		$data = getPendingChange($db, $type, $p1);

		switch ($type) {
			case MODERATIONCHANGE_LYRIC:
				$data["p2"] = dbStripSlashes($data["p2"]);
				$data["p3"] = dbStripSlashes($data["p3"]);
				$data["p2"] = dbAddSlashes($data["p2"]);
				$data["p3"] = dbAddSlashes($data["p3"]);

				updateLyric($db, $p1, $data["p2"], $data["p3"]);
				break;
				
			case MODERATIONCHANGE_RECORDNAME:
				updateRecord($db, $p1, $data["p2"]);
				break;
				
			case MODERATIONCHANGE_LYRICLINK:
				//nothing to do but remove pending change
				break;
				
			default:
				echo "unimplemented acceptPendingChange type ".$type;
		}

		dbQuery($db, "DELETE FROM tblPendingChanges WHERE type=".$type." AND p1=".$p1." AND p2='".$data["p2"]."'"); // AND p3='".$data["p3"]."'");
	}
	
?>