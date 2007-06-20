<?
	$config['feedback']['enabled'] = true;
	
	define('FEEDBACK_SUBMIT',	1);	//user submitted "site feedback", general comments

	//subjectId is the userId if its a abuse report
	function saveFeedback($_type, $_text, $_subject = 0)
	{
		global $db, $session;
		if (!is_numeric($_type) || !is_numeric($_subject)) return false;

		$q = 'INSERT INTO tblFeedback SET feedbackType='.$_type.',text="'.$db->escape($_text).'",userId='.$session->id.',subjectId='.$_subject.',timeCreated=NOW()';
		$db->insert($q);
	}

	function getFeedback($_type = 0, $_sql_limit = '')
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)'.$_sql_limit;
		if ($_type) $q .= 'WHERE t1.feedbackType='.$_type;
		return $db->getArray($q);
	}

	function getFeedbackCnt($_type = 0)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback';
		if ($_type) $q .= ' WHERE feedbackType='.$_type;
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