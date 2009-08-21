<?php
/**
 * $Id$
 *
 * API for http://is.gd/ URL shortening service
 *
 * API documentation: http://is.gd/api_info.php
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * Creates a short URL from input URL
 *
 * @param $url input URL
 * @return short URL or false on error
 */
function isgdShortURL($url)
{
	$res = file_get_contents('http://is.gd/api.php?longurl='.urlencode($url));

	if (substr($res, 0, 4) == 'http') return trim($res);
	echo 'Error: '.$res;
	return false;
}

?>
