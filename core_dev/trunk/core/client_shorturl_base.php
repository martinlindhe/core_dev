<?php
/**
 * $Id$
 */

require_once('client_http.php');

class shorturl_base
{
	private $api_url; //url for API
	private $cache_time; //default to 24 hours

	function setCacheTime($s) { $this->cache_time = $s; }

	function getUrl($url)
	{
		$u = new HttpClient($url);
		$u->setCacheTime($this->cache_time);

		return $u->get();
	}

}
