<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_shorturl_isgd.php');
require_once('client_shorturl_tinyurl.php');
require_once('client_shorturl_unu.php');

class shorturl
{
	const IS_GD       = 1;
	const U_NU        = 2;
	const TINYURL_COM = 3;

	private $client;

	function __construct($service = IS_GD)
	{
		switch ($service) {
		case shorturl::IS_GD:
			$this->client = new shorturl_isgd();
			break;

		case shorturl::U_NU:
			$this->client = new shorturl_unu();
			break;

		case shorturl::TINYURL_COM:
			$this->client = new shorturl_tinyurl();
			break;
		}
	}

	function getShortUrl($url)
	{
		return $this->client->getShortUrl($url);
	}
}



$url = 'http://developer.yahoo.com/yui/editor/';
$s = new shorturl(shorturl::TINYURL_COM);
echo $s->getShortUrl($url)."\n";


?>
