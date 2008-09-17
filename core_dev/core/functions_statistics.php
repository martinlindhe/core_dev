<?php
/**
 * $Id$
 *
 * Used by admin module to display statistics
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('STAT_NEW_USERS',			1);		///< number of new users created during this timeperiod	FIXME IMPLEMENT
define('STAT_NEW_BLOGS',			2);		///< number of new blogs created						FIXME IMPLEMENT
define('STAT_NEW_CHATMESSAGES',		3);		///< number of written chat messages					FIXME IMPLEMENT
define('STAT_NEW_FORUMPOSTS',		4);		///< number of new forum threads+posts created			FIXME IMPLEMENT
define('STAT_NEW_FILES',			5);		///< number of new files uploaded						FIXME IMPLEMENT
define('STAT_NEW_GUESTBOOK',		6);		///< number of written guestbook messages				FIXME IMPLEMENT
define('STAT_NEW_MESSAGES',			7);		///< number of written messages							FIXME IMPLEMENT
define('STAT_NEW_FEEDBACK',			8);		///< number of new feedback entries						FIXME IMPLEMENT

define('STAT_SUBSCRIPTIONS_FORUMS',	20);	///< number of started subscriptions (forum threads)	FIXME IMPLEMENT
define('STAT_SUBSCRIPTIONS_BLOGS',	21);	///< number of started subscriptions (blogs)			FIXME IMPLEMENT
define('STAT_SUBSCRIPTIONS_FILES',	22);	///< number of started subscriptions (files)			FIXME IMPLEMENT

define('STAT_VIEWS_PROFILES',		30);	///< number of views of user's profile pages			FIXME IMPLEMENT
define('STAT_VIEWS_BLOGS',			31);	///< number of views of all blogs						FIXME IMPLEMENT
define('STAT_VIEWS_FILES',			32);	///< number of views of all files						FIXME IMPLEMENT

define('STAT_COMMENTS_BLOGS',		40);	///< number of comments written to blogs				FIXME IMPLEMENT
define('STAT_COMMENTS_FILES',		41);	///< number of comments written to files/photos			FIXME IMPLEMENT

define('STAT_UNIQUE_LOGINS',		50);	///< number of unique logins							FIXME IMPLEMENT
define('STAT_TOTAL_LOGINS',			51);	///< number of logins (non-unique)						FIXME IMPLEMENT


//TODO: numbers on reminder-emails "you havent logged in for 30 days plz login!"
//		and numbers of successful logins after reminder-emails being sent out
define('STAT_REMINDERS_SENT',		60);	///< number of reminder-emails that was sent out		FIXME IMPLEMENT
define('STAT_REMINDERS_SUCCEEDED',	61);	///< number of reminder-emails that lead to a login		FIXME IMPLEMENT

/**
 * XXX
 */
function saveStat($type, $val, $timeStart, $timeEnd)
{
	global $db;
	if (!is_numeric($type) || !is_numeric($val)) return false;

	$q = 'SELECT id FROM tblStatistics WHERE type='.$type.' AND timeStart="'.$db->escape($timeStart).'" AND timeEnd="'.$db->escape($timeEnd).'"';
	if ($db->getOneItem($q)) {
		$q = 'UPDATE tblStatistics SET value='.$val.' WHERE type='.$type.' AND timeStart="'.$db->escape($timeStart).'" AND timeEnd="'.$db->escape($timeEnd).'" LIMIT 1';
		return $db->update($q);
	}
	$q = 'INSERT INTO tblStatistics SET type='.$type.',value='.$val.',timeStart="'.$db->escape($timeStart).'",timeEnd="'.$db->escape($timeEnd).'"';
	return $db->insert($q);
}


/**
 * XXX
 */
function getOldestLoginTime()
{
	global $db;

	$q = 'SELECT timeCreated FROM tblLogins ORDER BY timeCreated ASC LIMIT 1';
	return $db->getOneItem($q);
}

/**
 * XXX
 */
function getLoginCnt($time_start, $time_end, $distinct = false)
{
	global $db;
	if (!is_numeric($time_start) || !is_numeric($time_end)) return false;

	if ($distinct) {
		$q = 'SELECT COUNT(DISTINCT(userId)) FROM tblLogins';
	} else {
		$q = 'SELECT COUNT(userId) FROM tblLogins';
	}
	$q .= ' WHERE timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
	return $db->getOneItem($q);
}

?>
