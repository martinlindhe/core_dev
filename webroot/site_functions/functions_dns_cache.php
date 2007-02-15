<?
	/* DNS cache */
	function getDNSCacheHostname($geo_ip)
	{
		global $config, $geodb;
		
		if (!is_numeric($geo_ip) || !$geo_ip) return 'invalid IP';

		$sql = 'SELECT * FROM tblDNSCache WHERE IP='.$geo_ip.' LIMIT 1';
		$data = dbOneResult($geodb, $sql);
		
		if (!empty($data['host']) && $config['dns_cache_autolookup'] === false)
		{
			//Dont perform DNS lookup on previously cached DNS entries on manual use
			$host = $data['host'];
		}
		else if (!$data || ($data['timeCreated'] < time()-$config['dns_cache_expiration']))
		{
			//Perform DNS lookup on missing DNS entry or automatic run
			$host = updateDNSCache($geo_ip);
		}
		else
		{
			$host = $data['host'];
		}
		
		if ($host == GeoIP_to_IPv4($geo_ip)) {
			$host = '<span style="background-color: #EE7777;">'.$host.' - unresolved</span>';
		}
		
		return $host;
	}

	function updateDNSCache($geo_ip)
	{
		global $geodb;

		//missing or expired entry, lookup & update cache
		$sql = 'DELETE FROM tblDNSCache WHERE IP='.$geo_ip;
		dbQuery($geodb, $sql);

		$host = gethostbyaddr(GeoIP_to_IPv4($geo_ip));
		$host = dbAddSlashes($geodb, $host);
		$sql = 'INSERT INTO tblDNSCache SET IP='.$geo_ip.',timeCreated='.time().',host="'.$host.'"';
		dbQuery($geodb, $sql);

		return $host;
	}

	function getDNSCacheAge($geo_ip)
	{
		global $geodb;

		$sql = 'SELECT * FROM tblDNSCache WHERE IP='.$geo_ip;
		$data = dbOneResult($geodb, $sql);

		if (!$data) return false;
		return time()-$data['timeCreated'];
	}

	function getAllDNSCacheEntries()
	{
		global $geodb;

		$sql = 'SELECT * FROM tblDNSCache';
		return dbArray($geodb, $sql);
	}
?>