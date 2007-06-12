<?
	$config['feedback']['enabled'] = true;
	
	define('FEEDBACK_SUBMIT',	1);	//user submitted "site feedback", general comments
	define('FEEDBACK_ABUSE',	2);	//user submitted "user abuse" about someone

	//subjectId is the userId if its a abuse report
	function saveFeedback($_type, $_text, $_subject = 0)
	{
		global $db, $session;
		if (!is_numeric($_type) || !is_numeric($_subject)) return false;

		$q = 'INSERT INTO tblFeedback SET feedbackType='.$_type.',text="'.$db->escape($_text).'",userId='.$session->id.',subjectId='.$_subject.',timeCreated=NOW()';
		$db->insert($q);
	}

	function getFeedback($_type = 0)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		if ($_type) $q .= 'WHERE t1.feedbackType='.$_type;
		return $db->getArray($q);
	}

	function getFeedbackCnt($_type)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback WHERE feedbackType='.$_type;
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

	function abuseReport($_id)
	{
		if (!empty($_POST['report_reason']) || !empty($_POST['report_text'])) {
			saveFeedback(FEEDBACK_ABUSE, $_POST['report_reason'].': '.$_POST['report_text'], $_id);
		}

		echo '<h1>Abuse</h1>';
		echo 'If you want to block this user. Click here - fixme<br/><br/>';

		echo '<h2>Report user form</h2>';
		echo 'Please choose the reason as to why you wish to report this user:<br/>';
		echo '<form method="post" action="">';
		echo 'Reason: ';
		echo '<select name="report_reason">';
		echo '<option value=""></option>';
		echo '<option value="Harassment">Harassment</option>';
		echo '<option value="Other">Other</option>';
		echo '</select><br/>';

		echo 'Please describe your reason for the abuse report.<br/>';
		echo '<textarea name="report_text" rows="6" cols="40"></textarea><br/>';

		echo '<input type="submit" class="button" value="Send report"/>';
		echo '</form>';
	}
?>