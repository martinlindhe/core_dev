<?php
/**
 * $Id$
 *
 * Functions for tblRelations
 *
 * For relations between two values / names / numbers or something.
 * see functions_contacts.php for user friends/blocklist functions
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

define('RELATION_1TO1CHAT', 10);

/**
 * Used to load relation entries (where the tabel is
 *
 * @param $str string to look for in settingName or settingValue
 * @param $expire time (in minutes) from timeSaved that the entries are valid
 */
function loadRelation($_type, $_owner, $rel, $_expire = 0)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner) || !is_numeric($_expire)) return false;

	$q = 'SELECT * FROM tblRelations WHERE relationType='.$_type.' AND ownerId='.$_owner;
	if ($_expire) $q .= ' AND timeSaved >= DATE_SUB(NOW(),INTERVAL '.$_expire.' MINUTE)';
	$q .= ' AND (r1="'.$db->escape($rel).'" OR r2="'.$db->escape($rel).'")';
 	$q .= ' ORDER BY timeSaved DESC LIMIT 1';

	$res = $db->getOneRow($q);
	if (!$res) return false;

	return ($res['r1'] == $rel ? $res['r2'] : $res['r1']);
}

function saveRelation($_type, $_owner, $r1, $r2)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$q = 'INSERT INTO tblRelations SET relationType='.$type.',ownerId='.$_owner;
	$q .= ',r1="'.$db->escape($r1).'",r2="'.$db->escape($r2).'",timeSaved=NOW()';
	return $db->insert($q);
}

?>
