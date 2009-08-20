<?php
/**
 * $Id$
 *
 * API for http://u.nu/ URL shortening service
 *
 * API documentation: http://u.nu/unu-api
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * Creates a short URL from input URL
 *
 * @param $url input URL
 * @return short URL or false on error
 */
function unuShortURL($url)
{
	$res = file_get_contents('http://u.nu/unu-api-simple?url='.urlencode($url));

	if (substr($res, 0, 4) == 'http') return trim($res);

	list($error_code, $error_message) = explode('|', $res);
	echo 'Error: '.$error_message.' ('.$error_code.')';
	return false;
}

?>
