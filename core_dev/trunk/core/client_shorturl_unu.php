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

require_once('client_shorturl_base.php');

class shorturl_unu extends shorturl_base
{
	function __construct()
	{
		$this->api_url = 'http://u.nu/unu-api-simple';
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
