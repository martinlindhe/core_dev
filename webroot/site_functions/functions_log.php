<?
	//functions_log.php
	
	define('LOGLEVEL_ALL', 0);
	define('LOGLEVEL_NOTICE', 1);
	define('LOGLEVEL_WARNING', 2);
	define('LOGLEVEL_ERROR', 3);

	function logEntry(&$db, $string, $entryLevel = LOGLEVEL_NOTICE)
	{
		if (!is_numeric($entryLevel)) return false;
		
		$string = dbAddSlashes($db, trim($string));
		if (!$string) return false;

		$sql = 'INSERT INTO tblLogs SET entryText="'.$string.'", timeCreated=NOW(), entryLevel='.$entryLevel.', userId='.$_SESSION['userId'].',userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
		dbQuery($db, $sql);
		
		//todo: om loglevel är högre än notice, skicka automatiskt ett mail till admin
	}
	
	function getLogEntries(&$db, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel)) return false;

		$sql  = 'SELECT t1.*,t2.userName FROM tblLogs AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		if ($entryLevel != LOGLEVEL_ALL) {
			$sql .= 'WHERE t1.entryLevel >= '.$entryLevel.' ';
		}
		$sql .= 'ORDER BY t1.timeCreated DESC';

		return dbArray($db, $sql);
	}

	function getLogEntriesByGeoIP(&$db, $geoIP, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel) || !is_numeric($geoIP)) return false;

		$sql  = 'SELECT t1.*,t2.userName FROM tblLogs AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE userIP='.$geoIP.' ';
		if ($entryLevel != LOGLEVEL_ALL) {
			$sql .= 'AND t1.entryLevel >= '.$entryLevel.' ';
		}
		$sql .= 'ORDER BY t1.timeCreated DESC';

		return dbArray($db, $sql);
	}

	function getLogEntriesCount(&$db, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel)) return false;

		$sql  = 'SELECT COUNT(entryId) FROM tblLogs ';
		if ($entryLevel) {
			$sql .= 'WHERE entryLevel >= '.$entryLevel;
		}

		return dbOneResultItem($db, $sql);
	}

	function getLogEntriesCountByGeoIP(&$db, $geoIP, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel) || !is_numeric($geoIP)) return false;

		$sql  = 'SELECT COUNT(entryId) FROM tblLogs ';
		$sql .= 'WHERE userIP='.$geoIP;
		if ($entryLevel) {
			$sql .= ' AND entryLevel >= '.$entryLevel;
		}

		return dbOneResultItem($db, $sql);
	}

	function clearLog(&$db, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel)) return false;
		
		$sql = 'DELETE FROM tblLogs WHERE entryLevel >= '.$entryLevel;
		dbQuery($db, $sql);
	}
	
	function clearLogByGeoIP(&$db, $geoIP, $entryLevel = LOGLEVEL_ALL)
	{
		if (!is_numeric($entryLevel) || !is_numeric($geoIP)) return false;

		$sql = 'DELETE FROM tblLogs WHERE userIP='.$geoIP.' AND entryLevel >= '.$entryLevel;
		dbQuery($db, $sql);
	}

	function getUsernamesFromGeoIP(&$db, $geoIP)
	{
		if (!is_numeric($geoIP)) return false;

		$sql = 'SELECT DISTINCT t1.userId,t2.userName FROM tblLogs AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE t1.userIP='.$geoIP;
		
		$list = dbArray($db, $sql);
		
		$result = '';
		for ($i=0; $i<count($list); $i++) {
			$result .= nameLink($list[$i]['userId'], $list[$i]['userName']);
			if ($i<count($list)-1) $result .= ', ';
		}
		
		return $result;
	}
?>