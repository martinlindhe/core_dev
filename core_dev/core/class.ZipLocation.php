<?
/**
 * $Id$
 *
 * Looks up user location from entered zip code
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class ZipLocation
{
	/**
	 * Checks if the zip code is a valid one
	 */
	function isValid($zip)
	{
		global $db;
		$zip = trim($zip);
		if (!is_numeric($zip)) return false;

		$q = 'SELECT COUNT(*) FROM tblLocationZip WHERE zip='.$zip;
		if ($db->getOneItem($q)) return true;
		return false;
	}

	/**
	 * Returns text name representation of the zip code
	 */
	function describe($zip)
	{
		global $db;
		$zip = trim($zip);
		if (!is_numeric($zip)) return false;

		$q = 'SELECT t2.name AS ortName, t3.name AS lanName FROM tblLocationZip AS t1 '.
			'LEFT JOIN tblLocationOrt AS t2 ON (t1.ortId=t2.ortId) '.
			'LEFT JOIN tblLocationLan AS t3 ON (t1.lanId=t3.lanId) '.
			'WHERE t1.zip='.$zip;
		$row = $db->getOneRow($q);
		if (!$row) return false;

		return $row['ortName'].', '.$row['lanName'];
	}

}
?>