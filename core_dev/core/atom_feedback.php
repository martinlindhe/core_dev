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

function answerFeedback($_id, $_answer)
{
	global $db, $session;
	if (!is_numeric($_id) || !$session->id) return false;

	$q = 'UPDATE tblFeedback SET answer="'.$db->escape($_answer).'", answeredBy='.$session->id.',timeAnswered=NOW() WHERE feedbackId='.$_id;
	return $db->update($q);
}

/**
 * Returns objects in feedback queue
 */
function getFeedback($_type = 0, $_sql_limit = '')
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)';
	$q .= ' WHERE t1.answeredBy=0';
	if ($_type) $q .= ' AND t1.feedbackType='.$_type;
	$q .= $_sql_limit;

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
	$q .= ' WHERE answeredBy=0';
	if ($_type) $q .= ' AND feedbackType='.$_type;
	return $db->getOneItem($q);
}

/**
 * Get the number of new feedback entries during the specified time period
 */
function getFeedbackCountPeriod($dateStart, $dateStop)
{
	global $db;

	$q = 'SELECT count(feedbackId) AS cnt FROM tblFeedback WHERE timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
	return $db->getOneItem($q);
}

/**
 * Returns answered feedback entries
 */
function getAnsweredFeedback($_type = 0, $_sql_limit = '')
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)';
	$q .= ' WHERE t1.answeredBy != 0';
	if ($_type) $q .= ' AND t1.feedbackType='.$_type;
	$q .= $_sql_limit;

	return $db->getArray($q);
}

/**
 * Returns number of answered feedback entries
 */
function getAnsweredFeedbackCnt($_type = 0)
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback';
	$q .= ' WHERE answeredBy != 0';
	if ($_type) $q .= ' AND feedbackType='.$_type;
	return $db->getOneItem($q);
}




/**
 * Returns matches in question or answer text of answered feedback
 */
function searchFeedback($_type = 0, $_search, $_sql_limit = '')
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblFeedback AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId)';
	$q .= ' WHERE t1.answeredBy != 0 AND (t1.body LIKE "%'.$db->escape($_search).'%" OR t1.answer LIKE "%'.$db->escape($_search).'%")';
	if ($_type) $q .= ' AND t1.feedbackType='.$_type;
	$q .= $_sql_limit;

	return $db->getArray($q);
}

/**
 * Returns number of answered feedback entries
 */
function searchFeedbackCnt($_type = 0, $_search)
{
	global $db;
	if (!is_numeric($_type)) return false;

	$q  = 'SELECT COUNT(feedbackId) FROM tblFeedback';
	$q .= ' WHERE answeredBy != 0 AND (body LIKE "%'.$db->escape($_search).'%" OR answer LIKE "%'.$db->escape($_search).'%")';
	if ($_type) $q .= ' AND feedbackType='.$_type;
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
