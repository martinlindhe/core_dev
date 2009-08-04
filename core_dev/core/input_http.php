<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with HTTP requests
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

$config['http']['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

/**
 * Checks if input string is a valid http or https URL
 *
 * @param $url string
 * @return true if input is a url
 */
function is_url($url)
{
	if (strpos($url, ' ')) return false; //FIXME: the regexp allows spaces in domain name
	$pattern = "(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)";

	if (preg_match($pattern, $url)) return true;
	return false;
}

/**
 * Fetches header for given URL and returns it in a parsed array
 *
 * @param $url URL to request HTTP headers for
 * @return array of parsed headers or false on error
 */
function http_head($url)
{
	global $config;

	if (!is_url($url)) {
		echo $url." is not a valid URL\n";
		return false;
	}

	$u = parse_url($url);

	switch ($u['scheme']) {
		case 'http':
			$default_port = 80;
			break;

		//FIXME https support (?)
		default:
			echo "unsupported url scheme: ".$u['scheme']."\n";
			return;
	}

	if (empty($u['port'])) $u['port'] = $default_port;

	$fp = fsockopen($u['host'], $u['port'], $errno, $errstr, 30);
	if (!$fp) {
		echo "$errstr ($errno)\n";
		return false;
	}

	$query_header  = "HEAD ".$u['path'].(!empty($u['query']) ? '?'.$u['query'] : '')." HTTP/1.1\r\n";
	$query_header .= "Host: ".$u['host']."\r\n";
	$query_header .= "User-Agent: ".$config['http']['user_agent']."\r\n";	//XXX: core_dev + version?
	$query_header .= "Connection: close\r\n\r\n";
	fwrite($fp, $query_header);

	$result = '';
	while (!feof($fp)) {
		$result .= fgets($fp, 512);
	}
	fclose($fp);

	//Cut off header
	$pos = strpos($result, "\r\n\r\n");
	$result_header = substr($result, 0, $pos);

	$headers = explode("\r\n", $result_header);
	return $headers;
}

/**
 * Fetch HTTP status code of given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return HTTP status code, or false if none was found
  */
function http_status($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		switch (substr($h, 0, 9)) {
			case 'HTTP/1.0 ':
			case 'HTTP/1.1 ':
				return intval(substr($h, 9));
		}
	}
	return false;
}

/**
 * Returns the Last-Modified field from given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return a unix timestamp, or false if none was found
 */
function http_last_modified($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		$col = explode(': ', $h);
		switch ($col[0]) {
			case 'Last-Modified':
				return strtotime($col[1]);
		}
	}
	return false;
}

/**
 * Returns the Content-Length field from given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return content length
 */
function http_content_length($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		$col = explode(': ', $h);
		switch ($col[0]) {
			case 'Content-Length':
				return intval($col[1]);
		}
	}
	return 0;
}

?>
