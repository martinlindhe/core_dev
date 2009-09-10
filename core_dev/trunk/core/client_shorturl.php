<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_shorturl_isgd.php');
require_once('client_shorturl_tinyurl.php');
require_once('client_shorturl_trim.php');
require_once('client_shorturl_unu.php');

class shorturl
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
			$this->client = new shorturl_isgd();
			break;

		case shorturl::TINYURL_COM:
			$this->client = new shorturl_tinyurl();
			break;

		case shorturl::TR_IM:
			$this->client = new shorturl_trim();
			break;

		case shorturl::U_NU:
			$this->client = new shorturl_unu();
			break;
		}

		$this->client->setCacheTime(3600 * 24); //24 hours
	}

	function getShortUrl($url)
	{
		return $this->client->getShortUrl($url);
	}
}



$url = 'http://developer.yahoo.com/yui/editor/';
$s = new shorturl(shorturl::TR_IM);
echo $s->getShortUrl($url)."\n";


?>
