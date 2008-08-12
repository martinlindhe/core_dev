<?php
/**
 * $Id$
 *
 * Generic module to have users submit questions / suggestions to the site admins
 * Admins can then choose to comment on the feedback and have it published on the website.
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

$config['feedback']['enabled'] = true;
	
define('FEEDBACK_SUBMIT',	1);	//user submitted "site feedback", general comments

/**
 * subjectId is the userId if its a abuse report
 */
function saveFeedback($_type, $_subj, $_body = '', $_subjectId = 0)
{
	global $db, $session;
	if (!is_numeric($_type) || !is_numeric($_subjectId)) return false;

	$q = 'SELECT feedbackId FROM tblFeedback WHERE feedbackType='.$_type.' AND subj="'.$db->escape($_subj).'" AND body="'.$db->escape($_body).'" AND userId='.$session->id;
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblFeedback SET feedbackType='.$_type.',subj="'.$db->escape($_subj).'",body="'.$db->escape($_body).'",userId='.$session->id.',subjectId='.$_subjectId.',timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Returns objects in feedback queue
 */
function getFeedback($_type = 0, $_sql_limit = '')
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)'.$_sql_limit;
	if ($_type) $q .= 'WHERE t1.feedbackType='.$_type;
	return $db->getArray($q);
}

/**
 * Returns number of objects in feedback queue
 */
function getFeedbackCnt($_type = 0)
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback';
	if ($_type) $q .= ' WHERE feedbackType='.$_type;
	return $db->getOneItem($q);
}

/**
 * Delete specified feedback object
 */
function deleteFeedback($_id)
{
	global $db, $session;
	if (!$session->isAdmin || !is_numeric($_id)) return false;

	$q = 'DELETE FROM tblFeedback WHERE feedbackId='.$_id;
	return $db->delete($q);
}

/**
 * Returns specified feedback object
 */
function getFeedbackItem($_id)
{
	global $db, $session;
	if (!$session->isAdmin || !is_numeric($_id)) return false;

	$q = 'SELECT * FROM tblFeedback WHERE feedbackId='.$_id;
	return $db->getOneRow($q);
}
?>
