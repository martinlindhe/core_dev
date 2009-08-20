<?php
/**
 * $Id$
 */

function searchBands($query)
{
	global $db;
	$query = $db->escape(trim($query));
	if (!$query) return false;

	$q  = 'SELECT * FROM tblBands ';
	$q .= 'WHERE LCASE(bandName) LIKE LCASE("%'.$query.'%") OR LCASE(bandInfo) LIKE LCASE("%'.$query.'%") ';
	$q .= 'ORDER BY bandName ASC';
	return $db->getArray($q);
}

function addBand($band_name)
{
	global $db, $session;
	if (!$session->id) return false;

	$band_name = $db->escape(trim($band_name));

	$check = $db->getOneRow('SELECT * FROM tblBands WHERE bandName="'.$band_name.'"');
	if ($check) return false; //a band with this name already exists

	$q = 'INSERT INTO tblBands SET bandName="'.$band_name.'",creatorId='.$session->id.',timeCreated=NOW()';
	return $db->insert($q);
}

function updateBandInfo($band_id, $band_info)
{
	global $db;
	if (!is_numeric($band_id)) return false;

	$band_info = $db->escape($band_info);

	$db->update('UPDATE tblBands SET bandInfo="'.$band_info.'" WHERE bandId='.$band_id);
	return true;
}

function getBands()
{
	global $db;
	return $db->getArray('SELECT * FROM tblBands ORDER BY bandName ASC');
}

function getBandsWithRecordCount()
{
	global $db;
	$q = 'SELECT t1.*,
			(SELECT COUNT(recordId) FROM tblRecords WHERE bandId=t1.bandId) AS cnt FROM tblBands AS t1
			ORDER BY t1.bandName ASC';
	return $db->getArray($q);
}

function getBandName($_id)
{
	global $db;
	if (!is_numeric($_id)) return false;

	return $db->getOneItem('SELECT bandName FROM tblBands WHERE bandId='.$_id);
}

function setBandName($_id, $_name)
{
	global $db;
	if (!is_numeric($_id)) return false;

	$db->update('UPDATE tblBands SET bandName="'.$db->escape($_name).'" WHERE bandId='.$_id);
}

function getBandInfo($band_id)
{
	global $db;
	if (!is_numeric($band_id)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblBands AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'WHERE t1.bandId='.$band_id;
	return $db->getOneRow($q);
}

function bandCount()
{
	global $db;
	return $db->getOneItem('SELECT COUNT(bandId) FROM tblBands');
}

/* Returns the records that this band has made */
function getBandRecords($band_id)
{
	global $db;
	if (!is_numeric($band_id)) return false;

	return $db->getArray('SELECT t1.*, (SELECT COUNT(trackNumber) FROM tblTracks WHERE recordId=t1.recordId) AS cnt FROM tblRecords AS t1 WHERE t1.bandId='.$band_id.' ORDER BY t1.recordName ASC');
}

function getBandCompilations($band_id)
{
	global $db;
	if (!is_numeric($band_id)) return false;

	$q  = 'SELECT t2.recordId,t2.recordName, (SELECT COUNT(trackNumber) FROM tblTracks WHERE recordId=t2.recordId) AS cnt FROM tblTracks AS t1 ';
	$q .= 'LEFT JOIN tblRecords AS t2 ON (t1.recordId=t2.recordId) ';
	$q .= 'WHERE t1.bandId='.$band_id.' AND t2.bandId=0 ';
	$q .= 'GROUP BY t2.recordId ';
	$q .= 'ORDER BY t2.recordName ASC';
	return $db->getArray($q);
}

/* Returnerar låttitlar och id till alla låtar med detta band i alfabetisk ordning */
function getBandLyrics($band_id)
{
	global $db;
	if (!is_numeric($band_id)) return false;

	$q  = 'SELECT lyricId,lyricName FROM tblLyrics WHERE bandId='.$band_id;
	$q .= ' ORDER BY lyricName ASC';
	return $db->getArray($q);
}

function deleteBand($band_id)
{
	global $db, $session;
	if (!$session->isAdmin || !is_numeric($band_id)) return false;

	$q = 'DELETE FROM tblBands WHERE bandId='.$band_id;
	return $db->delete($q);
}
?>
