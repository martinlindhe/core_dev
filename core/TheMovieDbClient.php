<?php
/**
 * themoviedb.org metadata api
 * http://www.themoviedb.org/documentation/api
 *
 * @author Martin Lindhe, 2009-2014 <martin@ubique.se>
 */

//STATUS: API was working 2014-04-25

namespace cd;

require_once('CoreBase.php');
require_once('HttpClient.php');
require_once('Imdb.php');
require_once('MediaResource.php');
require_once('TempStore.php');

class TheMovieDbClient extends CoreBase
{
    public $api_key = '';

    public $language = 'en'; // 2 or 3-letter ISO-639 language code

    public $cache_results = false;

    public function __construct($api_key)
    {
        if (!$api_key)
            throw new \Exception ("API key required");

        $this->api_key = $api_key;
    }

    /** Is $s a tmdb id? */
    static function probeId($s)
    {
        //XXX verify id with regexp

        if (!is_numeric($s))
            return false;

        return true;
    }

    /**
     * Search for a movie
     * @return array with little info of each movie, use getInfo() to return more info
     */
    public function search($name)
    {
        if (!$name)
            return false;

        if ($this->cache_results) {
            $temp = TempStore::getInstance();
            $key = 'TheMovieDbClient/search//'.$name;

            $data = $temp->get($key);
            if ($data)
                return unserialize($data);
        }

        $url =
        'http://api.themoviedb.org/3/search/movie'.
        '?language='.$this->language.
        '&api_key='.$this->api_key.
        '&query='.urlencode($name);

//echo $url."\n";

        $http = new HttpClient($url);
        $data = $http->getBody();

        if ($http->getStatus() != 200) {
            d('TheMovieDbClient server error: '.$http->getStatus() );
            d( $http->getAllResponseHeaders() );
            return false;
        }

        $res = Json::decode($data);

        if ($this->cache_results) {
            $temp->set($key, serialize($res), '24h');
        }

        return $res;
    }

    /**
     * Returns details on a movie
     *
     * @param $movie_id TMDB id
     */
    public function getInfo($movie_id)
    {
        if (!self::probeId($movie_id))
            throw new \Exception ('not a tmdb id');

        if ($this->cache_results) {
            $temp = TempStore::getInstance();
            $key = 'TheMovieDbClient/info//'.$movie_id;

            $data = $temp->get($key);
            if ($data)
                return unserialize($data);
        }

        $url =
            'http://api.themoviedb.org/3/movie/'.$movie_id.
            '?language='.$this->language.
            '&api_key='.$this->api_key;

        $http = new HttpClient($url);
        $data = $http->getBody();

        if ($http->getStatus() != 200) {
            d('TheMovieDbClient getInfo server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        $res = Json::decode($data);

        if ($this->cache_results) {
            $temp->set($key, serialize($res), '24h');
        }

        return $res;
    }


}
