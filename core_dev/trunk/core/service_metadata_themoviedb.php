<?php
/**
 * $Id$
 *
 * themoviedb.org metadata api
 * http://api.themoviedb.org/2.1/
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: wip
//TODO: search by opensubtitles hash
//TODO: search by imdb id

require_once('class.CoreBase.php');
require_once('client_http.php');

class TheMovieDbMetadata extends CoreBase
{
	private $api_key;

	function __construct($api_key = '')
	{
		$this->setApiKey($api_key);
	}

	function setApiKey($key)
	{
		$this->api_key = $key;
	}

	function search($name)
	{
		if (!$name) return false;

		$url = 'http://api.themoviedb.org/2.1/Movie.search/en/xml/'.$this->api_key.'/'.urlencode($name);

		$http = new HttpClient($url);
		$http->setCacheTime(60*60*24); //24 hours

		$data = $http->getBody();

		if ($http->getStatus() != 200) {
			d('TheMovieDbMetadata->search server error: '.$http->getStatus() );
			d( $http->getHeaders() );
			return false;
		}

		d( $data);

	}
}
