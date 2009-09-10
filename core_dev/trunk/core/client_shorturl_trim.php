<?php
/**
 * $Id$
 *
 * API for http://tr.im/ URL shortening service
 *
 * API documentation: http://api.tr.im/website/api
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_shorturl_base.php');

class shorturl_trim extends shorturl_base
{
	function __construct()
	{
		$this->api_url = 'http://api.tr.im/v1/trim_simple';
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
		echo 'Error: '.$res;
		return false;
	}
}

?>
