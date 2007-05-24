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
		
		$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)';
		return $db->getArray($q);
	}

	function getFeedbackCnt()
	{
		global $db;
		
		$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback';
		return $db->getOneItem($q);
	}

	function deleteFeedback($_id)
	{
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblFeedback WHERE feedbackId='.$_id;
		return $db->delete($q);
	}

	function getFeedbackItem($_id)
	{
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblFeedback WHERE feedbackId='.$_id;
		return $db->getOneRow($q);
	}
?>