<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//TODO: Handle IPv6 addresses (transparently)
//TODO: is_ipv4() and is_ipv6() regexp matchers

/**
 * Converts a IPv4 address to GeoIP format
 *
 * @param $ipv4 IPv4 address in 123.123.123.123 format
 * @return 32bit unsigned value (GeoIP formatted IPv4 address)
 */
function IPv4_to_GeoIP($ipv4)
{
	if (is_numeric($ipv4)) return $ipv4;

	$arr = explode('.', trim($ipv4));
	if (count($arr) != 4) return 0;

	$num = ($arr[0]*16777216) + ($arr[1]*65536) + ($arr[2]*256) + $arr[3];
	return $num;
}

/**
 * Converts a GeoIP address to human readable format
 *
 * @param $geoip 32bit unsigned value (GeoIP formatted IPv4 address)
 * @return IPv4 address in 123.123.123.123 format
 */
function GeoIP_to_IPv4($geoip)
{
	if (!is_numeric($geoip)) return 0;
	settype($geoip, 'float');

	$w = ($geoip / 16777216) % 256;
	$x = ($geoip / 65536   ) % 256;
	$y = ($geoip / 256     ) % 256;
	$z = ($geoip           ) % 256;
	if ($z < 0) $z += 256;

	return $w.'.'.$x.'.'.$y.'.'.$z;
}

/**
 * Data taken from http://en.wikipedia.org/wiki/Ipv4 (Reserved address blocks)
 *
 * @param $ip IPv4 address in GeoIP or human readable format
 * @return true if specified IPv4 address is reserved
 */
function reservedIP($ip)
{
	if (!is_numeric($ip)) $ip = IPv4_to_GeoIP($ip);

	if ($ip <=   16777215) return true;							//0.0.0.0/8			Current network (only valid as source address)
	if ($ip >=  167772160 && $ip <=  184549375) return true;	//10.0.0.0/8		Private network
	if ($ip >= 2130706432 && $ip <= 2147483647) return true;	//127.0.0.0/8		Loopback
	if ($ip >= 2147483648 && $ip <= 2147549183) return true;	//128.0.0.0/16		Reserved (IANA)
	if ($ip >= 2851995648 && $ip <= 2852061183) return true;	//169.254.0.0/16	Link-Local
	if ($ip >= 2886729728 && $ip <= 2887778303) return true;	//172.16.0.0/12		Private network
	if ($ip >= 3221159936 && $ip <= 3221225471) return true;	//191.255.0.0/16	Reserved (IANA)
	if ($ip >= 3221225472 && $ip <= 3221225727) return true;	//192.0.0.0/24		Reserved (IANA)
	if ($ip >= 3221225984 && $ip <= 3221226239) return true;	//192.0.2.0/24		Documentation and example code
	if ($ip >= 3227017984 && $ip <= 3227018239) return true;	//192.88.99.0/24	IPv6 to IPv4 relay
	if ($ip >= 3232235520 && $ip <= 3232301055) return true;	//192.168.0.0/16	Private network
	if ($ip >= 3323068416 && $ip <= 3323199487) return true;	//198.18.0.0/15		Network benchmark tests
	if ($ip >= 3758096128 && $ip <= 3758096383) return true;	//223.255.255.0/24 	Reserved (IANA)
	if ($ip >= 3758096384 && $ip <= 4026531839) return true;	//224.0.0.0/4		Multicasts (former Class D network)
	if ($ip >= 4026531840 && $ip <= 4294967295) return true;	//240.0.0.0/4		Reserved (former Class E network)
																//255.255.255.255 	Broadcast
	return false;
}

/**
 * Checks if client IP address is in the whitelist
 * Useful to create simple IP access rules
 *
 * @param $whitelist array of IPv4 addresses
 * @return true if client IP address is in the $allowed list
 */
function allowedIP($whitelist)
{
	if (php_sapi_name() == 'cli') return true;

	$ip = IPv4_to_GeoIP(client_ip());

	return match_ip($ip, $whitelist);
}

/**
 * If the IPv4 address in $ip is found to match a rule in $matches
 * then the function returns true
 *
 * @param $ip IPv4 address in GeoIP or human readable format
 * @param $matches array of IPv4 addresses
 * @return true if $ip address is found in the $matches list
 */
function match_ip($ip, $matches)
{
	if (!is_numeric($ip)) $ip = IPv4_to_GeoIP($ip);

	foreach ($matches as $chk) {
		$a = explode('/', $chk);	//check against "80.0.0.0/8" format
		if (count($a) == 2) {
			$lo = IPv4_to_GeoIP($a[0]);
			if ($ip >= $lo) {
				$hi = $lo+bindec('1'.str_repeat('0', 32-$a[1])) - 1;	//XXX this should be possible to do easier!
				//echo "lo: ".GeoIP_to_IPv4($lo)."   (".$lo.")\n";
				//echo "hi: ".GeoIP_to_IPv4($hi)."   (".$hi.")\n";
				if ($ip <= $hi) return true;
			}
		} else if ($ip == IPv4_to_GeoIP($chk)) return true;
	}

	return false;
}

/**
 * Returns client IP address as a literal string
 */
function client_ip()
{
	if (php_sapi_name() == 'cli') return '127.0.0.1';
	return $ip = $_SERVER['REMOTE_ADDR'];
}

/**
 * Checks if input string is a valid URL
 *
 * @param $url string
 * @return true if input is a url
 */
function is_url($url)
{
	if (strpos($url, ' ')) return false; //FIXME: the regexp allows spaces in domain name
	$pattern = "((https?|rtmpe?|mms|rtsp)://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)";

	if (preg_match($pattern, $url))
		return true;

	return false;
}

/**
 * Checks if input string is a valid email address
 *
 * @param $adr string
 * @return true if input is a email address
 */
function is_email($adr)
{
	$pattern = '/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/';
	if (preg_match($pattern, $adr))
		return true;

	return false;
}

/**
 * @return default port for protocol $scheme
 */
function scheme_default_port($scheme)
{
	$schemes = array('http'=>80, 'https'=>443, 'rtsp'=>554, 'rtmp'=>1935, 'rtmpe'=>1935, 'mms'=>1755);

	if (empty($schemes[$scheme]))
		return false;

	return $schemes[$scheme];
}

?>
