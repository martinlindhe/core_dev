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
//TODO: store movie title + imdb id in db

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

	/**
	 * Search for a movie
	 */
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

		$hits = $this->parseSearchResult($data);

		if (!$hits)
			return false;

		//find and return best match
		$score = 0.0;
		$idx   = 0;
		for ($i=0; $i<count($hits); $i++)
		{
			if ($hits[$i]['score'] > $score) {
				if ($this->debug) d('promoting '.$i.' match');
				$idx = $i;
				$score = $hits[$i]['score'];
			}
		}

		return $hits[ $idx ];
	}

	/**
	 * Returns details on a movie
	 *
	 * @param $tmdb_id TMDB id
	 */
	function getInfo($tmdb_id)
	{
		if (!is_numeric($tmdb_id))
			return false;

		$url = 'http://api.themoviedb.org/2.1/Movie.getInfo/en/xml/'.$this->api_key.'/'.$tmdb_id;

		$http = new HttpClient($url);
		$http->setCacheTime(60*60*24); //24 hours

		$data = $http->getBody();

		if ($http->getStatus() != 200) {
			d('TheMovieDbMetadata->getInfo server error: '.$http->getStatus() );
			d( $http->getHeaders() );
			return false;
		}

		$res = $this->parseSearchResult($data);
		return $res[0];
	}

	private function parseSearchResult($data)
	{
		$movies = array();

		$reader = new XMLReader();
		if ($this->debug) echo 'Parsing movies: '.$data.ln();
		$reader->xml($data);

		while ($reader->read())
		{
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'movies')
				break;

			if ($reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($reader->name) {
			case 'movies': break;

			case 'movie':
				$movies[] = $this->parseMovie($reader);
				break;

			default:
				//TODO LATER: themoviedb xml exposes opensearch xml tags, see if that can be used
				//echo "parseSearchResult unknown ".$reader->name.ln();
				break;
			}
		}

		$reader->close();
		return $movies;
	}

	private function parseMovie($reader)
	{
		$id       = '';
		$name     = '';
		$imdb     = '';
		$summary  = '';
		$released = '';
		$score    = ''; //0.0 to 1.0 match score
		$images   = array();
		while ($reader->read()) {
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'movie') {
				//XXX cache write title + id combo
				return array('title'=>$name, 'id'=>$id, 'imdb'=>$imdb, 'score'=>$score, 'summary'=>$summary, 'released'=>$released, 'images'=>$images);
			}

			if ($reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($reader->name) {
			case 'name':
				$reader->read();
				$name = $reader->value;
				break;

			case 'imdb_id':
				$reader->read();
				$imdb = $reader->value;
				break;

			case 'id':
				$reader->read();
				$id = $reader->value;
				break;

			case 'score':
				$reader->read();
				$score = $reader->value;
				break;

			case 'overview':
				$reader->read();
				$summary = $reader->value;
				break;

			case 'released': //release date
				$reader->read();
				$released = $reader->value;
				break;

			case 'images': break;

			case 'image':
/*
				//TODO: rewrite logic to select 1 of each image type
				if ($reader->getAttribute('type') == 'poster' && $reader->getAttribute('size') != 'mid') break;
				if ($reader->getAttribute('type') == 'backdrop' && $reader->getAttribute('size') != 'poster') break;
*/
				$images[] = array(
				'type'=> $reader->getAttribute('type'),
				'url' => $reader->getAttribute('url'),
				'size'=> $reader->getAttribute('size'),
				'id'  => $reader->getAttribute('id') );
				break;

			case 'popularity': break;
			case 'alternative_name': break;
			case 'type': break; //"movie"
			case 'url': break;
			case 'rating': break;

			case 'runtime': break; //in minutes, XXX store as Duration
			case 'budget': break;
			case 'revenue': break;
			case 'homepage': break;
			case 'trailer': break; //XXX youtube trailer

			case 'countries': break;

			//XXX parse studios properly
			case 'studios': break;
			case 'studio': break;

/*
      <categories>
        <category type="genre" url="http://themoviedb.org/encyclopedia/category/878" name="Science Fiction"/>
      </categories>
*/
			//XXX parse categories properly
			case 'categories': break;
			case 'category': break;

			//XXX parse cast properly
			case 'cast': break;
			case 'person': break;


			default: echo "parseMovie bad entry " .$reader->name.ln();
			}
		}
	}
}
