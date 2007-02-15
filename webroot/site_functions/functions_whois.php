<?
	//whois module default settings:
	$config['whois']['cache_expire'] = (3600*24)*7;	//7 days cache expire

	$config['whois']['arin']['host'] = 'whois.arin.net';
	$config['whois']['arin']['port'] = 43;


	//Does a WHOIS query to $whois_server, returning the raw data
	function WHOIS_Query($geoip, $whois_server)
	{
		if (!is_numeric($geoip) || IgnoredIPRange($geoip)) return false;
		
		$ipv4 = GeoIP_to_IPv4($geoip);
		
		$fp = fsockopen($whois_server['host'], $whois_server['port'], $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
			die;
		}

		fwrite($fp, $ipv4);
		fwrite($fp, "\n");

		$data = '';
		while (!feof($fp)) {
			$data .= fgets($fp, 128);
		}
		fclose($fp);
		
		return $data;
	}

	//WHOIS database lookup. performs a initial request to ARIN & redirects lookups if nessecary
	function getRawWhoisData($geoip)
	{
		global $config;
		
		if (!is_numeric($geoip) || IgnoredIPRange($geoip)) return false;

		//1. Perform lookup at ARIN, if nessecary - redirect lookup to RIPE or APNIC
		$data = WHOIS_Query($geoip, $config['whois']['arin']);

		$arin_data = parse_ARIN_WHOIS_data($data);
		if (empty($arin_data['host'])) {
			return $arin_data;
		}

		echo 'ARIN redirected me to '.$arin_data['host'].'<br>';

		$data = WHOIS_Query($geoip, $arin_data);
		$ripe_data = parse_RIPE_WHOIS_data($data);
		$ripe_data['source'] = $arin_data['source'];

		return $ripe_data;
	}
	
	//Parses ARIN whois data, looking for 'ReferralServer'
	function parse_ARIN_WHOIS_data($raw_data)
	{
		$result['clean_name'] = '';
		$result['clean_address'] = '';
		$result['clean_phone'] = '';

		$result['address'] = '';
		$result['city'] = '';
		$result['stateprov'] = '';
		$result['postalcode'] = '';
		$result['country'] = '';
		
		$result['inetnum_start'] = 0;
		$result['inetnum_end'] = 0;
		
		$result['source'] = 'arin';
		
		$list = explode("\n", $raw_data);
		
		//echo 'parsing ARIN data!<br>';
		//echo '<pre>'; print_r($list);

		for ($i=0; $i<count($list); $i++) {
			
			$pos = strpos($list[$i], ':');
			if ($pos === false) continue;
			
			$part1 = substr($list[$i], 0, $pos);
			$part2 = trim(substr($list[$i], $pos+1));
			
			switch ($part1)
			{
			//ReferralServer: whois://whois.ripe.net:43

			//Todo 'rwhois' support:
			//http://www.dnsstuff.com/tools/whois.ch?ip=66.27.124.7
			//ReferralServer: rwhois://ipmt.rr.com:4321
				case 'ReferralServer':
					$arr = parse_url($part2);

					if ($arr['scheme'] == 'whois') {
						$result['host'] = $arr['host'];
						if (!empty($arr['port'])) $result['port'] = $arr['port'];
						else $result['port'] = 43;
						
						switch ($result['host']) {
							case 'whois.ripe.net':
								/* WHOIS server for Europe, Middle East */
								$result['source'] = 'ripe';
								break;
								
							case 'whois.apnic.net':
								/* WHOIS server for China, India - same format as RIPE */
								$result['source'] = 'apnic';
								break;
								
							case 'whois.lacnic.net':
								/* WHOIS server for Brazil (.br) */
								$result['source'] = 'lacnic';
								//$result['host'] = 'whois.registro.br';
								break;
								
							case 'whois.afrinic.net':
								/* WHOIS server for Egypt (.eg) */
								$result['source'] = 'afrinic';
								break;

							default:
								echo '<b>ERROR: Unknown whois host: '.$result['host'].'</b><br>';
								return false;
						}
						return $result;
					}
					break;

				case 'OrgName':
					$result['clean_name'] = $part2;
					break;
					
				case 'Address':
					$result['address'] = $part2;
					break;

				case 'City':
					$result['city'] = $part2;
					break;

				case 'StateProv':
					$result['stateprov'] = $part2;
					break;

				case 'PostalCode':
					$result['postalcode'] = $part2;
					break;

				case 'Country':
					$result['country'] = $part2;
					break;

				case 'NetRange':
					//NetRange: 8.0.0.0 - 8.255.255.255
					$temp = parse_cidr($part2);

					$result['inetnum_start'] = $temp['inetnum_start'];
					$result['inetnum_end']   = $temp['inetnum_end'];
					break;
					
				case 'RTechPhone':
				case 'OrgTechPhone':
					//OrgTechPhone: +1-248-265-5000
					//RTechPhone:  +1-905-333-7055
					//we use this as 'phone' data for now
					$result['clean_phone'] = $part2;
					break;
			}
		}
		
		//fix up address
		$result['clean_address'] = $result['address']."\n".$result['city'].', '.$result['stateprov'].' '.$result['postalcode']."\n".$result['country'];

		return $result;
	}
	
	//Parses raw WHOIS data (from RIPE) and return an array
	//Also supports LACNIC WHOIS data (inetnum:     201.13/16, no "descr" tag)
	//http://www.rfc-editor.org/rfc/rfc3912.txt "WHOIS protocol specification (2004, current)"
	function parse_RIPE_WHOIS_data($raw_data)
	{
		//Fill array with whois data
		$data = explode("\n", $raw_data);
		
		$result = array();
		
		for ($i=0; $i<count($data); $i++) {
			
			$curr = trim($data[$i]);
			if (!$curr || substr($curr,0,1) == '%') continue;
			
			$pos = strpos($curr, ':');
			if ($pos !== false) {
				$curr_object = substr($curr, 0, $pos);
				$curr_data = trim(substr($curr, $pos+1));
				if (!$curr_data) continue;
				
				//om $curr_data enbart innehåller tecknet = eller -, skippa raden
				$check = count_chars($curr_data, 1);
				if (!empty($check[ ord('=') ])) if (strlen($curr_data) == $check[ ord('=') ]) continue;
				if (!empty($check[ ord('-') ])) if (strlen($curr_data) == $check[ ord('-') ]) continue;

				if (!isset($result[$curr_object]['__INDEX'] )) $result[$curr_object]['__INDEX'] = 0;
				else $result[$curr_object]['__INDEX']++;
				
			} else {
				$curr_data = trim($curr);
			}

			if (!isset($result[$curr_object][ $result[$curr_object]['__INDEX'] ])) $result[$curr_object][ $result[$curr_object]['__INDEX'] ] = '';
			$result[$curr_object][ $result[$curr_object]['__INDEX'] ] .= $curr_data."\n";
		}

		//Clean up the result
		//The important data we return is: inetnum_start, inetnum_end
		//		clean_name, clean_address, clean_phone
		$result['inetnum_start'] = 0;
		$result['inetnum_end'] = 0;

		if (!empty($result['inetnum'][0])) {
			$temp = parse_cidr($result['inetnum'][0]);

			$result['inetnum_start'] = $temp['inetnum_start'];
			$result['inetnum_end']   = $temp['inetnum_end'];
		}

		if (!empty($result['org-name'][0])) {
			$result['clean_name'] = trim($result['org-name'][0]);
		} else if (!empty($result['owner'][0])) {
			//LACNIC WHOIS data uses 'owner' tag
			$result['clean_name'] = trim($result['owner'][0]);
		} else {
			$result['clean_name'] = trim($result['descr'][0]);
		}

		//Only returns the first line
		$pos = strpos($result['clean_name'], "\n");
		if ($pos !== false) {
			$result['clean_name'] = trim(substr($result['clean_name'], 0, $pos));
		}
		
		//Removes ending . or , from 'clean_name'
		if (substr($result['clean_name'], -1, 1) == '.') $result['clean_name'] = substr($result['clean_name'], 0, -1);
		if (substr($result['clean_name'], -1, 1) == ',') $result['clean_name'] = substr($result['clean_name'], 0, -1);
		
		//Returns first sentence in 'clean_name', if multiple sentences,
		//example: "Hudiksvalls kommun public network. For internet and application servers and employee communication"
		$pos = strrpos($result['clean_name'], '. ');
		if ($pos !== false) {
			$part1 = substr($result['clean_name'], 0, $pos);
			$part2 = substr($result['clean_name'], $pos+1);
			
			//echo '2nd sentence is '. strlen($part2).'<br>';
			if (strlen($part2) >= 30) {
				//Only return the first sentence
				$result['clean_name'] = trim($part1);
			}
		}
		
		//Some string cleanup for 'clean_name':
		//$result['clean_name'] = str_replace(' , ', ', ', $result['clean_name']);
		$result['clean_name'] = str_replace('<F6>', '&ouml;', $result['clean_name']);
		
		
		$adr = '';
		for ($i=0; $i<count($result['address']); $i++) {
			if (empty($result['address'][$i])) continue;
			$temp = trim($result['address'][$i]);

			//Remove comment part
			$pos = strpos($temp, '#');
			if ($pos !== false) $temp = trim(substr($temp, 0, $pos));
			
			$adr .= $temp."\n";
		}
		$result['clean_address'] = trim($adr);

		//Remove all data after a line starting with ***** (comments are embedded like this)
		$pos = strpos($result['clean_address'], "\n*****");
		if ($pos !== false) {
			$result['clean_address'] = trim(substr($result['clean_address'], 0, $pos));
		}
		
		$result['clean_phone'] = '';
		if (!empty($result['phone'][0])) $result['clean_phone'] = trim($result['phone'][0]);
		
		/* Remove comment part, as in:
		phone:        +00 00         # unknown phone number				http://www.dnsstuff.com/tools/whois.ch?ip=192.36.68.1
		*/
		$pos = strpos($result['clean_phone'], '#');
		if ($pos !== false) {
			$result['clean_phone'] = trim(substr($result['clean_phone'], 0, $pos));
		}
		//Clean up phone number: remove spaces, - signs, "(0)", ( and )
		$result['clean_phone'] = str_replace(' ', '', $result['clean_phone']);
		$result['clean_phone'] = str_replace('-', '', $result['clean_phone']);
		$result['clean_phone'] = str_replace('(0)', '', $result['clean_phone']);
		
		//(11) 3156-0100 []   (brazilian phone number)
		
		$result['clean_phone'] = str_replace('(', '', $result['clean_phone']);
		$result['clean_phone'] = str_replace(')', '', $result['clean_phone']);

		$result['clean_phone'] = str_replace('[]', '', $result['clean_phone']);
		if ($result['clean_phone'] && substr($result['clean_phone'], 0, 1) != '+') {
			//Add + prefix
			$result['clean_phone'] = '+'.$result['clean_phone'];
		}


		//Convert all data to UTF-8 for internal use
		$result['clean_name'] = mb_convert_encoding($result['clean_name'], 'UTF-8', 'ASCII');
		$result['clean_address'] = mb_convert_encoding($result['clean_address'], 'UTF-8', 'ASCII');
		$result['clean_phone'] = mb_convert_encoding($result['clean_phone'], 'UTF-8', 'ASCII');

		return $result;
	}

	//Returns WHOIS data from the database, by IP
	function getCachedWhoisEntry($geoip)
	{
		global $geodb;

		if (!is_numeric($geoip) || IgnoredIPRange($geoip)) return false;

		$sql  = 'SELECT * FROM tblWHOIS WHERE '.$geoip.' BETWEEN geoIP_start AND geoIP_end LIMIT 1';
		return dbOneResult($geodb, $sql);
	}

	//Returns WHOIS data from the database, by entryId
	function getCachedWhoisEntryByID($entryId)
	{
		global $geodb;

		if (!is_numeric($entryId)) return false;

		$sql  = 'SELECT * FROM tblWHOIS WHERE entryId='.$entryId;
		return dbOneResult($geodb, $sql);
	}

	//Accepts a IP as parameter, and then does a request to the RIPE whois database, then updates local database
	// if local database dont have this entry, or the entry is outdated
	function getWhoisData($geoip)
	{
		global $config, $geodb;

		if (!is_numeric($geoip) || IgnoredIPRange($geoip)) return false;

		//1a. om den finns i databasen, returnera
		$whois = getCachedWhoisEntry($geoip);
		if ($whois) {
			if ($whois['timeUpdated'] >= time() - $config['whois']['cache_expire']) {
				return $whois;
			}
		}

		//2. annars, skaffa & uppdatera
		forceWHOISCacheUpdate($geoip);

		return getCachedWhoisEntry($geoip);
	}
	
	//Returns the cached name for this entry. If it is not cached, a lookup will be performed
	function getWHOISCacheName($geoip)
	{
		$data = getWhoisData($geoip);
		
		if (!$data) return '';
		
		return $data['name'];
	}
	
	//Forces an update of the WHOIS cache specified IP
	function forceWHOISCacheUpdate($geoip)
	{
		global $geodb;

		if (!is_numeric($geoip) || IgnoredIPRange($geoip)) return false;
		
		$arr = getRawWhoisData($geoip);
		if (!$arr) return false;
		
		if ($arr['inetnum_start'] == 0 || $arr['inetnum_end'] == 0) {
			//Avoid inserts of crap entries
			echo '<b>ERROR: WHOIS lookup on '.GeoIP_to_IPv4($geoip).' returned bogus data!</b><br>';
			return false;
		} else {
			//fixme: ta bort debugkod:
			if (!empty($_SESSION['loggedIn']) && $_SESSION['userName'] == 'martin') echo '.';
		}
		

		$name = dbAddSlashes($geodb, $arr['clean_name']);
		$address = dbAddSlashes($geodb, $arr['clean_address']);
		$phone = dbAddSlashes($geodb, $arr['clean_phone']);
		$source = dbAddSlashes($geodb, $arr['source']);

		$sql = 'SELECT * FROM tblWHOIS WHERE geoIP_start='.$arr['inetnum_start'].' AND geoIP_end='.$arr['inetnum_end'].' LIMIT 1';
		$whois = dbOneResult($geodb, $sql);

		if ($whois && $whois['entryId']) {
			//Update existing entry
			$sql = 'UPDATE tblWHOIS SET timeUpdated='.time().',name="'.$name.'",address="'.$address.'",phone="'.$phone.'",source="'.$source.'" WHERE entryId='.$whois['entryId'];
			dbQuery($geodb, $sql);
		} else {
			//Write a new entry
			$sql = 'INSERT INTO tblWHOIS SET timeUpdated='.time().',geoIP_start='.$arr['inetnum_start'].',geoIP_end='.$arr['inetnum_end'].',name="'.$name.'",address="'.$address.'",phone="'.$phone.'",source="'.$source.'"';
			dbQuery($geodb, $sql);
		}
	}
	
	//returns all WHOIS cache entries, for use with update_whois_cache.php
	function getAllWHOISCacheEntries($countryId = 0)
	{
		if (!is_numeric($countryId)) return false;

		global $geodb;
		
		if ($countryId) {
			$sql  = 'SELECT t1.* FROM tblWHOIS AS t1 ';
			$sql .= 'INNER JOIN tblGeoIP AS t2 ON (t1.geoIP_start BETWEEN t2.start AND t2.end) ';
			$sql .= 'WHERE t2.ci='.$countryId.' ';
			$sql .= 'ORDER BY t1.geoIP_start ASC';
		} else {
			$sql = 'SELECT * FROM tblWHOIS ORDER BY geoIP_start ASC';
		}

		return dbArray($geodb, $sql);
	}
	
	//Returns a list of country ID's ordered by popularity, of the WHOIS cache entries in database
	function getWHOISCacheCountryRanges()
	{
		global $geodb;
		
		$sql  = 'SELECT t2.ci FROM tblWHOIS AS t1 ';
		$sql .= 'INNER JOIN tblGeoIP AS t2 WHERE (t1.geoIP_start BETWEEN t2.start AND t2.end) ';
		$sql .= 'GROUP BY ci';
		
		return dbArray($geodb, $sql);
	}
	
	
	//Returns WHOIS data for the specified IP range, or false if the ip range dont exist in database
	function getExactWHOISData($geoip_start, $geoip_end)
	{
		global $geodb;

		if (!is_numeric($geoip_start) || IgnoredIPRange($geoip_start)) return false;
		if (!is_numeric($geoip_end) || IgnoredIPRange($geoip_end)) return false;

		$sql = 'SELECT * FROM tblWHOIS WHERE geoIP_start='.$geoip_start.' AND geoIP_end='.$geoip_end.' LIMIT 1';
		$data = dbOneResult($geodb, $sql);

		//Cleanup result
		//$data['name'] = htmlspecialchars($data['name'], ENT_NOQUOTES, 'UTF-8');
		
		return $data;
	}

	//Deletes the WHOIS entry with the specified entryId
	function deleteWHOISEntry($entryId)
	{
		global $geodb;

		if (!is_numeric($entryId)) return false;

		$sql = 'DELETE FROM tblWHOIS WHERE entryId='.$entryId;
		dbQuery($geodb, $sql);
	}

	function markWHOISRangePrivate($geoip_start, $geoip_end, $value)
	{
		global $geodb;

		if (!is_numeric($geoip_start) || !is_numeric($geoip_end)) return false;
		
		if (!$value) $value = 0;

		$sql = 'UPDATE tblWHOIS SET privateRange='.$value.' WHERE geoIP_start='.$geoip_start.' AND geoIP_end='.$geoip_end;
		dbQuery($geodb, $sql);
	}
	
	//Returns all cached IP ranges that $geoip fits into
	function getMatchingIPRanges($geoip)
	{
		global $geodb;

		if (!is_numeric($geoip)) return false;
		
		$sql = 'SELECT * FROM tblWHOIS WHERE '.$geoip.' BETWEEN geoIP_start AND geoIP_end';
		return dbArray($geodb, $sql);
	}

	/* Parses IP ranges and returns an array with 'inetnum_start' and 'inetnum_end' entries */
	//More info: http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
	function parse_cidr($address)
	{
		$address = trim($address);
		if (!$address) return false;

		$pos = strpos($address, '-');
		if ($pos !== false) {
			//Parse ARIN format that looks like this:
			//inetnum:      213.100.0.0 - 213.100.127.255
			$result['inetnum_start'] = IPv4_to_GeoIP(substr($address, 0, $pos));
			$result['inetnum_end'] = IPv4_to_GeoIP(substr($address, $pos+1));
		} else {
			//Parse LACNIC format that looks like this:
			//inetnum:     201.13/16			(borde bli 201.13.0.0 - 201.13.255.255)
				
			//echo 'parsing CIDR inetnum '.$address.'...<br>';

			$ip_arr = explode('/', $address);
			$delims = substr_count($ip_arr[0], '.');

			switch ($delims)
			{
				case 1:
					//Example: 201.13/16
					$ip = ip2long($ip_arr[0].'.0.0');
					break;
					
				case 2:
					//Example: 24.232.38/23
					$ip = ip2long($ip_arr[0].'.0');
					break;

				case 3:
					//Example: 192.168.1.0/24
					$ip = ip2long($ip_arr[0]);
					break;

				default:
					echo '<b>FATAL: parse_cidr() unsupported number of delimiters! '.$delims.'<br>';
					echo 'ip_arr: '.$ip_arr[0].'<br>';
					die;
			}

			$x = ip2long($ip_arr[1]);
     	$mask = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);			

			$nw = ($ip & $mask);
			$bc = $nw | (~$mask);

/*
			echo "Host Range:        " . long2ip($nw) . " -> " . long2ip($bc)  . "<br>";
			echo 'Start IP: '. long2ip($ip).'<br>';
			echo 'X: '.long2ip($x).'<br>';
			echo 'Mask: '.long2ip($mask).'<br>';
*/
			$result['inetnum_start'] = IPv4_to_GeoIP(long2ip($nw));
			$result['inetnum_end'] = IPv4_to_GeoIP(long2ip($bc));
		}

		return $result;
	}
?>