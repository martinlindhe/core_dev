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
?>