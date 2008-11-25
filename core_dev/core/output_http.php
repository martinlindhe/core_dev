<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('input_mime.php');

/**
 * Encodes parameters to a HTTP GET/POST request
 * For GET requests in URL
 * For POST requests with "Content-Type: application/x-www-form-urlencoded"
 */
function http_encode_params($params)	//XXX: can "http_build_query" be used instead?
{
	$res = '';
	foreach ($params as $key => $val) {
		$res .= $key.'='.urlencode($val).'&';
	}

	return substr($res, 0, strlen($res)-1);
}

/**
 * Performs a HTTP POST request on given url
 */
function http_post($url, $data)
{
	$p = parse_url($url);

	switch ($p['scheme']) {
		case 'http':
			$port = 80;
			break;

		case 'https':
			$port = 443;
			break;

		default:
			echo "http_post() unhandled scheme '".$p['scheme']."'\n";
			return false;
	}
	if (!empty($p['port'])) $port = $p['port'];

	$handle = @fsockopen($p['host'], $port, $errno, $errstr, 30);
	if (!$handle) return false;

	$params = http_encode_params($data);

	$h =
		"POST ".$p['path']." HTTP/1.0\r\n".
		"Host: ".$p['host']."\r\n".
		"Content-Type: application/x-www-form-urlencoded\r\n".
		"Content-Length: ".strlen($params)."\r\n".
		"User-Agent: core_dev\r\n".	//XXX version
		"\r\n".
		$params;

	if ($p['scheme'] == 'https') {
		//FIXME: how to start a https session? example here http://www.nusphere.com/kb/phpmanual/wrappers.http.htm?/
		echo "http_post() HTTPS not supported\n";
		return false;
	}

	fwrite($handle, $h);

	$data = '';
	while (!feof($handle)) {
		$data .= fgets($handle, 1024);
	}
	fclose($handle);

	//Separate HTTP response from MIME data
	$x = explode("\r\n", $data, 2);
	$status = explode(" ", $x[0]);
	$res['status'] = intval($status[1]);
	$data = $x[1];

	//Separate header from body of HTTP response
	$pos = strpos($data, "\r\n\r\n");
	if (!$pos) {
		$res['header'] = mimeParseHeader($data);
		$res['body'] = '';
	} else {
		$res['header'] = mimeParseHeader(substr($data, 0, $pos));
		$res['body'] = trim(substr($data, $pos + strlen("\r\n\r\n")));
	}
	return $res;
}

?>
