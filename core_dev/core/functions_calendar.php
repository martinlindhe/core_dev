<?
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

function getCalendars($_type, $_owner = 0)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$q = 'SELECT * FROM tblCalendar WHERE type='.$_type;
	if ($_owner) $q .= ' AND ownerId='.$_owner;
	return $db->getArray($q);
}

?>
