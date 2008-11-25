<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('input_mime.php');

/**
 * Encodes parameters to a HTTP GET/POST request
 */
function http_encode_params($params)
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
function http_post($url, $data, $port = 80)
{
	$p = parse_url($url);
	if ($p['scheme'] != 'http') {
		echo "http_post() unhandled scheme '".$p['scheme']."'\n";
		return false;
	}

	$handle = @fsockopen($p['host'], $port, $errno, $errstr, 30);
	if (!$handle) return false;

	$params = http_encode_params($data);

	$h =
		"POST ".$p['path']." HTTP/1.0\r\n".
		"Host: ".$p['host']."\r\n".
		"Content-Type: application/x-www-form-urlencoded;\r\n".
		"Content-Length: ".strlen($params)."\r\n".
		"User-Agent: core_dev\r\n".	//XXX version
		"\r\n".
		$params;

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
