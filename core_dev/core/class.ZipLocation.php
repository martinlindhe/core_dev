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

		$q = 'SELECT t2.name AS cityName, t3.name AS regionName FROM tblLocationZip AS t1 '.
			'LEFT JOIN tblLocationCity AS t2 ON (t1.cityId=t2.cityId) '.
			'LEFT JOIN tblLocationRegion AS t3 ON (t1.regionId=t3.regionId) '.
			'WHERE t1.zip='.$zip;
		$row = $db->getOneRow($q);
		if (!$row) return false;

		return $row['cityName'].', '.$row['regionName'];
	}

	/**
	 * Returns text name of city for zip code
	 */
	function city($zip)
	{
		global $db;
		$zip = trim($zip);
		if (!is_numeric($zip)) return false;

		$q = 'SELECT t2.name AS cityName FROM tblLocationZip AS t1 '.
			'LEFT JOIN tblLocationCity AS t2 ON (t1.cityId=t2.cityId) '.
			'WHERE t1.zip='.$zip;
		$row = $db->getOneRow($q);
		if (!$row) return false;

		return $row['cityName'];
	}

	/**
	 * Returns city id for zip code
	 */
	function cityId($zip)
	{
		global $db;
		$zip = trim($zip);
		if (!is_numeric($zip)) return false;

		$q = 'SELECT cityId FROM tblLocationZip WHERE zip='.$zip;
		return $db->getOneItem($q);
	}

	/**
	 * Returns region id for zip code
	 */
	function regionId($zip)
	{
		global $db;
		$zip = trim($zip);
		if (!is_numeric($zip)) return false;

		$q = 'SELECT regionId FROM tblLocationZip WHERE zip='.$zip;
		return $db->getOneItem($q);
	}

	/**
	 * Returns a XHTML block of all possible region selections
	 */
	function regionSelect()
	{
		global $db;

		$q = 'SELECT * FROM tblLocationRegion ORDER BY name ASC';
		$list = $db->getArray($q);

		$result = '<select name="x">';
		foreach ($list as $row) {
			$result .= '<option value="'.$row['regionId'].'">'.$row['name'].'</option>';
		}
		$result .= '</select>';
		return $result;
	}

	/**
	 * Returns a XHTML block of all possible region selections
	 */
	function citySelect($regionId)
	{
		global $db;
		if (!is_numeric($regionId)) return false;

		$q = 'SELECT * FROM tblLocationCity WHERE regionId='.$regionId.' ORDER BY name ASC';
		$list = $db->getArray($q);

		$result = '<select name="y">';
		foreach ($list as $row) {
			$result .= '<option value="'.$row['cityId'].'">'.$row['name'].'</option>';
		}
		$result .= '</select>';
		return $result;
	}
}
?>