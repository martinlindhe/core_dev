<?
	define("MODERATION_BAND",	1);
	define("MODERATION_RECORD",	2);
	define("MODERATION_LYRIC",	3);
	
	define("MODERATIONCHANGE_LYRICLINK",	1);
	define("MODERATIONCHANGE_LYRIC",		2);
	define("MODERATIONCHANGE_RECORDNAME",	3);


	//lägg till ett nyskapat objekt i modereringskön
	function addModerationItem($id, $type)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($id)) return false;
		
		$db->query('INSERT INTO tblNewAdditions SET ID='.$id.',type='.$type.',timestamp='.time());
	}
	
	function isModerated($id, $type)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($id)) return false;

		$check = $db->getOneItem('SELECT ID FROM tblNewAdditions WHERE ID='.$id.' AND type='.$type);
		if ($check) return true;
		return false;
	}
	
	function countNewAdditions()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(*) FROM tblNewAdditions');
	}
	
	/* Returns all new additions that needs to be moderated, oldest first */
	function getNewAdditions()
	{
		global $db;

		return $db->getArray('SELECT * FROM tblNewAdditions ORDER BY timestamp ASC');
	}
	
	function acceptNewAddition($type, $id)
	{
		if (!is_numeric($type) || !is_numeric($id)) return false;

		removeNewAddition($type, $id);
	}

	function denyNewAddition($type, $id)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($id)) return false;

		removeNewAddition($type, $id);
		switch ($type) {
			case MODERATION_BAND:
				$db->query('DELETE FROM tblBands WHERE bandId='.$id);
				$db->query('DELETE FROM tblRecords WHERE bandId='.$id);
				$db->query('DELETE FROM tblTracks WHERE bandId='.$id);
				$db->query('DELETE FROM tblLyrics WHERE bandId='.$id);
				break;

			case MODERATION_RECORD:
				$db->query('DELETE FROM tblRecords WHERE recordId='.$id);
				$db->query('DELETE FROM tblTracks WHERE recordId='.$id);
				break;

			case MODERATION_LYRIC:
				$db->query('DELETE FROM tblLyrics WHERE lyricId='.$id);
				break;
		}
	}

	function removeNewAddition($type, $id)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($id)) return false;

		$db->query('DELETE FROM tblNewAdditions WHERE ID='.$id.' AND type='.$type);
	}


	//--- modify existing data-functions:
	
	
	function addPendingChange($type, $p1, $p2, $p3="")
	{
		global $db;

		if (!is_numeric($type)) return false;
		
		if ($type == MODERATIONCHANGE_LYRICLINK) {	//db, type, record_id, track_id
			if (!is_numeric($p1) || !is_numeric($p2)) return false;
			$db->query("INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',timestamp=".time());
			
		} else if ($type == MODERATIONCHANGE_LYRIC) { //db, type, lyric_id, title, text
			if (!is_numeric($p1)) return false;
			
			$p2 = $db->escape($p2);
			$p3 = $db->escape($p3);
			$db->query("INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',p3='".$p3."',timestamp=".time());
			
		} else if ($type == MODERATIONCHANGE_RECORDNAME) { //db, type, record_id, title
			if (!is_numeric($p1)) return false;

			$p2 = $db->escape($p2);
			$db->query("INSERT INTO tblPendingChanges SET type=".$type.",p1=".$p1.",p2='".$p2."',timestamp=".time());

		} else {
			echo "addPendingChange(): unknown TYPE: ".$type;
			die;
		}
	}
	
	function countPendingChanges()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(*) FROM tblPendingChanges');
	}
	
	function isPendingChange($type, $p1, $p2 = '')
	{
		global $db;

		if ($type == MODERATIONCHANGE_LYRICLINK) {
			if (!is_numeric($p1) || !is_numeric($p2)) return false;
			$check = $db->getOneItem('SELECT type FROM tblPendingChanges WHERE p1='.$p1.' AND p2='.$p2);
			if ($check) return true;
		} else if ($type == MODERATIONCHANGE_LYRIC) {
			if (!is_numeric($p1)) return false;
			$check = $db->getOneItem('SELECT type FROM tblPendingChanges WHERE p1='.$p1);
			if ($check) return true;

		} else if ($type == MODERATIONCHANGE_RECORDNAME) {
			if (!is_numeric($p1)) return false;
			$check = $db->getOneItem('SELECT type FROM tblPendingChanges WHERE p1='.$p1);
			if ($check) return true;

		} else {
			die('addPendingChange(): unknown TYPE: '.$type);
		}

		return false;
	}
	
	//returnerar älst först
	function getPendingChanges()
	{
		global $db;

		return $db->getArray('SELECT * FROM tblPendingChanges ORDER BY timestamp ASC');
	}
	
	function denyPendingChange($type, $p1)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($p1)) return false;

		$db->query('DELETE FROM tblPendingChanges WHERE type='.$type.' AND p1='.$p1);
	}

	function getPendingChange($type, $p1)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($p1)) return false;

		return $db->getOneRow('SELECT * FROM tblPendingChanges WHERE type='.$type.' AND p1="'.$p1.'"');
	}

	function acceptPendingChange($type, $p1)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($p1)) return false;
		
		$data = getPendingChange($type, $p1);

		switch ($type) {
			case MODERATIONCHANGE_LYRIC:
				$data["p2"] = stripslashes($data["p2"]);
				$data["p3"] = stripslashes($data["p3"]);
				$data["p2"] = $db->escape($data["p2"]);
				$data["p3"] = $db->escape($data["p3"]);

				updateLyric($p1, $data["p2"], $data["p3"]);
				break;
				
			case MODERATIONCHANGE_RECORDNAME:
				updateRecord($p1, $data["p2"]);
				break;

			case MODERATIONCHANGE_LYRICLINK:
				//nothing to do but remove pending change
				break;

			default:
				echo "unimplemented acceptPendingChange type ".$type;
		}

		$db->query("DELETE FROM tblPendingChanges WHERE type=".$type." AND p1=".$p1." AND p2='".$data["p2"]."'"); // AND p3='".$data["p3"]."'");
	}
	
?>