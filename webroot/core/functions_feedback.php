<?
	function saveFeedback($_text)
	{
		global $db, $session;

		$q = 'INSERT INTO tblFeedback SET text="'.$db->escape($_text).'",userId='.$session->id.',timeCreated=NOW()';
		$db->insert($q);
	}

	function getFeedback()
	{
		global $db;
		
		$q = 'SELECT * FROM tblFeedback';
		return $db->getArray($q);
	}

?>