<?
	/* Takes a IPv4 address in the form 123.123.123.123 and returns a GeoIP address */
	function IPv4_to_GeoIP($ip)
	{
		if (is_numeric($ip)) return $ip;

		$iparr = explode('.', trim($ip));
		if (count($iparr) != 4) return 0;

		$ipnum = ($iparr[0]*16777216) + ($iparr[1]*65536) + ($iparr[2]*256) + $iparr[3];
		return $ipnum;
	}

	/* Takes a GeoIP address in the form 32bit unsigned integer and returns a IPv4 address */
	function GeoIP_to_IPv4($ip)
	{
		if (!is_numeric($ip)) return 0;
		settype($ip, 'float');

		$w = ($ip / 16777216) % 256;
		$x = ($ip / 65536   ) % 256;
		$y = ($ip / 256     ) % 256;
		$z = ($ip           ) % 256;

		if ($z < 0) $z+=256;

		return $w.'.'.$x.'.'.$y.'.'.$z;
	}

	function IgnoredIPRange($ipu)
	{
		//These are ignored IP's, they dont belong to any country and cannot be used together with the geoip database.
		//List of ignored IP ranges are from: http://en.wikipedia.org/wiki/Classful_network, 'Special ranges' section
		if ($ipu <= 16777215) return true;																//0.0.0.0     - 0.255.255.255
		if (($ipu >= 167772160) && ($ipu <= 184549375)) return true;			//10.0.0.0    - 10.255.255.255
		if (($ipu >= 2130706433) && ($ipu <= 2147483647)) return true;		//127.0.0.0   - 127.255.255.255
		if (($ipu >= 2851995648) && ($ipu <= 2852061183)) return true;		//169.254.0.0 - 169.254.255.255
		if (($ipu >= 2886729728) && ($ipu <= 2887778303)) return true;		//172.16.0.0  - 172.31.255.255
		if (($ipu >= 3221225984) && ($ipu <= 3221226239)) return true;		//192.0.2.0   - 192.0.2.255
		if (($ipu >= 3227017984) && ($ipu <= 3227018239)) return true;		//192.88.99.0 - 192.88.99.255
 		if (($ipu >= 3232235521) && ($ipu <= 3232301055)) return true;		//192.168.0.1 - 192.168.255.255
		if (($ipu >= 3323068416) && ($ipu <= 3323199487)) return true;		//198.18.0.0  - 198.19.255.255
		if (($ipu >= 3758096384) && ($ipu <= 4026531839)) return true;		//224.0.0.0   - 239.255.255.255
		if ($ipu >= 4026531840) return true;															//240.0.0.0   - 255.255.255.255

		return false; //IP is not ignored
	}
?>