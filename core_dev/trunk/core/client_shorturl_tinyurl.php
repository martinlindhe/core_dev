<?php
/**
 * $Id$
 *
 * API for http://tinyurl.com/ URL shortening service
 *
 * API documentation: http://fyneworks.blogspot.com/2008/08/tiny-url-api.html
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_shorturl.php');

class Shorturl_Tinyurl extends ShorturlBase
{
	function __construct()
	{
		$this->api_url = 'http://tinyurl.com/api-create.php';
	}

	/**
	 * Creates a short URL from input URL
	 *
	 * @param $url input URL
	 * @return short URL or false on error
	 */
	function getShortUrl($url)
	{
		$res = $this->getUrl($this->api_url.'?url='.urlencode($url));

		if (substr($res, 0, 4) == 'http') return trim($res);

		list($error_code, $error_message) = explode('|', $res);
		echo 'Error: '.$error_message.' ('.$error_code.')';
		return false;
	}
}

?>
