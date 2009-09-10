<?php

require_once('client_http.php');

class shorturl_base
{
	private $api_url; //url for API

	function getUrl($url)
	{
		$u = new http($url);
		$u->setCacheTime(3600*24); //24h

		return $u->get();
	}

}
