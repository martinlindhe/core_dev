<?
	
	/* Returns a list of all countries in db */
	function getCountries($db) {
		$sql = "SELECT * FROM tblCountries ORDER BY countryName ASC";
		
		return dbArray($db, $sql);
	}
	
	function getCountryName($db, $countryId) {
		if (!is_numeric($countryId)) return false;
		
		$sql = "SELECT countryName FROM tblCountries WHERE countryId = ".$countryId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);
		return $row["countryName"];
	}
	
	function getCountryBySuffix($db, $countrySuffix) {
		$countrySuffix = addslashes(trim($countrySuffix));
		
		$sql = "SELECT * FROM tblCountries WHERE countrySuffix='".$countrySuffix."'";
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
?>