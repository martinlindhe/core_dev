<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: finished

require_once('class.CoreBase.php');
require_once('client_http.php');
require_once('client_shorturl_isgd.php');
require_once('client_shorturl_tinyurl.php');
require_once('client_shorturl_trim.php');
require_once('client_shorturl_unu.php');

class ShorturlBase
{
	private $api_url;    ///< url for API
	private $cache_time; ///< defaults to 24 hours

	function setCacheTime($s) { $this->cache_time = $s; }

	function getUrl($url)
	{
		$http = new HttpClient($url);
		$http->setCacheTime($this->cache_time);

		return $http->getBody();
	}
}

class Shorturl extends CoreBase
{
	const IS_GD       = 1;
	const TINYURL_COM = 2;
	const TR_IM       = 3;
	const U_NU        = 4;

	private $client;

	function __construct($service = TR_IM)
	{
		switch ($service) {
		case shorturl::IS_GD:
			$this->client = new Shorturl_isgd();
			break;

		case shorturl::TINYURL_COM:
			$this->client = new Shorturl_tinyurl();
			break;

		case shorturl::TR_IM:
			$this->client = new Shorturl_trim();
			break;

		case shorturl::U_NU:
			$this->client = new Shorturl_unu();
			break;
		}

		$this->client->setCacheTime(3600 * 24); //24 hours
	}

	function getShortUrl($url)
	{
		return $this->client->getShortUrl($url);
	}
}

?>
