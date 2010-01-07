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

require_once('client_shorturl.php');

class Shorturl_isgd extends ShorturlBase
{
	function __construct()
	{
		$this->api_url = 'http://is.gd/api.php';
	}

	/**
	 * Creates a short URL from input URL
	 *
	 * @param $url input URL
	 * @return short URL or false on error
	 */
	function getShortUrl($url)
	{
		$res = $this->getUrl($this->api_url.'?longurl='.urlencode($url));

		if (substr($res, 0, 4) == 'http') return trim($res);
		echo 'Error: '.$res;
		return false;
	}
}

?>
