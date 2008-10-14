<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with HTTP requests
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

$config['http']['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

/**
 * Return the status code of given URL, or false on network error
 */
function http_status($url)
{
	global $config;

	$u = parse_url($url);

	switch ($u['scheme']) {
		case 'http':
			$default_port = 80;
			break;

		default:
			//TODO handle https
			echo "unsupported url scheme: ".$u['scheme']."\n";
			return;
	}

	if (empty($u['port'])) $u['port'] = $default_port;

	$fp = fsockopen($u['host'], $u['port'], $errno, $errstr, 30);
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
		return false;
	}

	$query_header  = "HEAD ".$u['path'].(!empty($u['query']) ? '?'.$u['query'] : '')." HTTP/1.1\r\n";
	$query_header .= "Host: ".$u['host']."\r\n";
	$query_header .= "User-Agent: ".$config['http']['user_agent']."\r\n";
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

	foreach ($headers as $h) {
		switch (substr($h, 0, 9)) {
			case 'HTTP/1.0 ':
			case 'HTTP/1.1 ':
				return intval(substr($h, 9));
		}
	}
	return false;
}

?>
