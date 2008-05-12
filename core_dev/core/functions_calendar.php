<?php
/**
 * $Id$
 *
 * Implements friend lists. also implements blocked contacts
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('functions_locale.php');	//for translation

///< Calendar types
define('CALENDAR_USER',		1);	///< Personal calendar - XXX implement
define('CALENDAR_SITE',		2);	///< Site wide calendar - XXX implement
define('CALENDAR_SERVICE',	3);	///< Service wide calendar. tblCalendar.ownerId = service id

function addCalendar($_type, $_owner, $begin, $end, $desc)
{
	global $db, $session;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$begin = sql_datetime(datetime_to_timestamp($begin));
	$end = sql_datetime(datetime_to_timestamp($end));

	$q = 'INSERT INTO tblCalendar SET type='.$_type.', ownerId='.$_owner.',creatorId='.$session->id.',timeBegin="'.$begin.'",timeEnd="'.$end.'", info="'.$db->escape($desc).'"';
	$db->insert($q);
}

function updateCalendar($_type, $_id, $begin, $end, $desc)
{
	global $db, $session;
	if (!is_numeric($_type) || !is_numeric($_id)) return false;

	$begin = sql_datetime(datetime_to_timestamp($begin));
	$end = sql_datetime(datetime_to_timestamp($end));

	$q = 'UPDATE tblCalendar SET timeBegin="'.$begin.'",timeEnd="'.$end.'", info="'.$db->escape($desc).'" WHERE type='.$_type.' AND entryId='.$_id;
	$db->update($q);
}

function deleteCalendar($_type, $_id)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_id)) return false;

	$q = 'DELETE FROM tblCalendar WHERE type='.$_type.' AND entryId='.$_id;
	$db->delete($q);
}

function getCalendars($_type, $_owner = 0)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$q = 'SELECT * FROM tblCalendar WHERE type='.$_type;
	if ($_owner) $q .= ' AND ownerId='.$_owner;
	return $db->getArray($q);
}

/**
 * Returns all calendars active within specified timespan 
 */
function getActiveCalendars($_type, $_owner, $ts)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner) || !is_numeric($ts)) return false;

	$ts = sql_datetime($ts);

	$q = 'SELECT * FROM tblCalendar WHERE type='.$_type;
	if ($_owner) $q .= ' AND ownerId='.$_owner;
	$q .= ' AND "'.$ts.'" BETWEEN timeBegin AND timeEnd';
	return $db->getArray($q);
}

function getCalendar($_type, $_owner, $_id)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner) || !is_numeric($_id)) return false;

	$q = 'SELECT * FROM tblCalendar WHERE type='.$_type;
	$q .= ' AND ownerId='.$_owner;
	$q .= ' AND entryId='.$_id;
	return $db->getOneRow($q);
}


?>
