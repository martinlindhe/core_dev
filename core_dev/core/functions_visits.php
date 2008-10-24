<?php
/**
 * $Id$
 *
 * Simple functions to enable visit logs for various objects
 */

//FIXME: rename to atom_visits.php

define('VISIT_USERPAGE',	1);
define('VISIT_FILE',		2);
define('VISIT_BLOG',		3);	//FIXME implement!

/**
 * Creates a visit log entry
 */
function logVisit($_type, $_owner)
{
	global $db, $session;
	if (!$session->id || !is_numeric($_type) || !is_numeric($_owner)) return false;

	//only log the latest entry for each visitor
	$q = 'DELETE FROM tblVisits WHERE type='.$_type.' AND ownerId='.$_owner.' AND creatorId='.$session->id;
	$db->delete($q);

	$q = 'INSERT INTO tblVisits SET type='.$_type.',ownerId='.$_owner.',creatorId='.$session->id.',timeCreated=NOW()';
	$db->insert($q);
}

/**
 * Returns visits
 */
function getVisits($_type, $_id, $_limit = 5)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_id) || !is_numeric($_limit)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblVisits AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'WHERE ownerId='.$_id.' AND type='.$_type.' ORDER BY timeCreated DESC';
	if ($_limit) $q .= ' LIMIT 0,'.$_limit;
	return $db->getArray($q);
}

/**
 * Get the number of visits during the specified time period
 */
function getVisitsCountPeriod($_type, $dateStart, $dateStop)
{
	global $db;

	if (!is_numeric($_type)) return false;

	$q = 'SELECT count(visitId) AS cnt FROM tblVisits WHERE type = '.$_type.' AND timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
	return $db->getOneItem($q);
}

?>
