<?php
/**
 * $Id$
 *
 * Parses MIME formatted email messages
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

$config['mime']['allowed_mime_types'] = array('text/plain', 'image/jpeg', 'image/png', 'video/3gpp');

/**
 * Email parser
 *
 * @param $msg raw email text from mail server
 * @param $callback callback function to execute after mail is parsed
 * @return all attachments, body & header nicely parsed up
 */
function mimeParseMail($msg, $callback = '')
{
	//Separate header from mail body
	$pos = strpos($msg, "\r\n\r\n");
	if ($pos === false) return false;

	$header = mimeParseHeader(substr($msg, 0, $pos));
	$body = trim(substr($msg, $pos + strlen("\r\n\r\n")));
	$res = mimeParseAttachments($header, $body);

	if (function_exists($callback)) {
		return call_user_func($callback, $res);
	}
	return $res;
}

/**
 * Parses a string of email headers into an array
 * XXX: limitation - multiple keys with same name will just be
 * 		glued together (Received are one such common header key)
 */
function mimeParseHeader($raw_head)
{
	$arr = explode("\n", $raw_head);
	$header = array();

	foreach ($arr as $row)
	{
		$pos = strpos($row, ': ');
		if ($pos) $curr_key = substr($row, 0, $pos);
		if (!$curr_key) die('super error');
		if (empty($header[ $curr_key ])) {
			$header[ $curr_key ] = substr($row, $pos + strlen(': '));
		} else {
			$header[ $curr_key ] .= $row;
		}

		$header[ $curr_key ] = str_replace("\r", ' ', $header[ $curr_key ]);
		$header[ $curr_key ] = str_replace("\n", ' ', $header[ $curr_key ]);
		$header[ $curr_key ] = str_replace("\t", ' ', $header[ $curr_key ]);
		$header[ $curr_key ] = str_replace('  ', ' ', $header[ $curr_key ]);
		$header[ $curr_key ] = trim($header[ $curr_key ]);
	}

	return $header;
}

/**
 * Parses and decodes attachments
 */
function mimeParseAttachments(&$header, &$body)
{
	global $config;

	$att = array();

	$content = explode(';', $header['Content-Type']);

	//Content-Type: text/plain; charset=ISO-8859-1; format=flowed
	//Content-Type: multipart/mixed; boundary="------------020600010407070807000608"
	switch ($content[0]) {
		case 'text/plain':
			//returns main message as an text/plain attachment also
			$att[0]['mimetype'] = $content[0];
			$att[0]['header'] = $header;
			$att[0]['body'] = $body;
			return $att;
	}

	$multipart_id = '';
	foreach ($content as $part)
	{
		$part = trim($part);
		if ($part == 'multipart/mixed' || $part == 'multipart/related') {
			continue;
		}

		$pos = strpos($part, '=');
		if ($pos === false) die("multipart header err\n");
		$key = substr($part, 0, $pos);
		$val = substr($part, $pos+1);

		switch ($key) {
			case 'boundary':
				$multipart_id = '--'.str_replace('"', '', $val);
				break;

			default:
				echo "Unknown param: ".$key." = ".$val."\n";
				break;
		}
	}
	if (!$multipart_id) die('didnt find multipart id');

	//echo "Splitting msg using id '".$multipart_id."'\n";

	//Parses attachments into array
	$part_cnt = 0;
	do {
		$p1 = strpos($body, $multipart_id);
		$p2 = strpos($body, $multipart_id, $p1+strlen($multipart_id));

		if ($p1 === false || $p2 === false) {
			echo "p1: ".$p1.", p2: ".$p2."\n";
			die("error parsing attachment\n");
		}

		//$current contains a whole block with attachment & attachment header
		$current = substr($body, $p1 + strlen($multipart_id), $p2 - $p1 - strlen($multipart_id));

		$head_pos = strpos($current, "\r\n\r\n");
		if ($head_pos) {
			$a_head = trim(substr($current, 0, $head_pos));
			$a_body = trim(substr($current, $head_pos+2));
		} else {
			die("error: '".$current."'\n");
		}

		$att[ $part_cnt ]['head'] = mimeParseHeader($a_head);
		$att[ $part_cnt ]['body'] = $a_body;
		$body = substr($body, $p2);

		$params = explode('; ', $att[ $part_cnt ]['head']['Content-Type']);
		$att[ $part_cnt ]['mimetype'] = $params[0];

		if (!empty($att[ $part_cnt ]['head']['Content-Location'])) $att[ $part_cnt ]['filename'] = $att[ $part_cnt ]['head']['Content-Location'];
		if (empty($att[ $part_cnt ]['filename'])) {
			//Extract name from [Content-Type] => image/jpeg; name="header.jpg"
			//or                [Content-Type] => image/jpeg; name=DSC00071.jpeg
			if (isset($params[1]) && substr($params[1], 0, 5) == 'name=') {
				$att[ $part_cnt ]['filename'] = str_replace('"', '', substr($params[1], 5) );
			}
		}

		if (!in_array($att[ $part_cnt ]['mimetype'], $config['mime']['allowed_mime_types'])) {
			echo "Unknown mime type: ". $att[ $part_cnt ]['mimetype']."\n";
			continue;
		}

		switch ($att[ $part_cnt ]['head']['Content-Transfer-Encoding']) {
			case '7bit':
				break;

			case '8bit':
				break;

			case 'base64':
				$att[ $part_cnt ]['body'] = base64_decode($att[ $part_cnt ]['body']);
				break;

			default:
				echo "Unknown transfer encoding: '". $att[ $part_cnt ]['head']['Content-Transfer-Encoding']."'\n";
				break;
		}

		$part_cnt++;

	} while ($body != $multipart_id.'--');

	return $att;
}

?>
