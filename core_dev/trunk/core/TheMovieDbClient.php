<?php
/**
 * $Id$
 *
 * themoviedb.org metadata api
 * http://api.themoviedb.org/2.1/
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip


//XXX: cache parsed result using TempStore

//TODO: search by opensubtitles hash
//TODO: search by imdb id
//TODO: store movie title + imdb id in db

//XXX export categories, no longer in the xml?

require_once('class.CoreBase.php');
require_once('HttpClient.php');
require_once('Imdb.php');
require_once('MediaResource.php');

class TheMovieDbMovie
{
    var $title;
    var $language;
    var $tmdb_id;
    var $imdb_id;
    var $rating;
    var $overview;
    var $release_date;
    var $last_modified; // how old is this data?

    var $images = array(); // array of ImageResource objects
}

class TheMovieDbClient extends CoreBase
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

        $res = $this->parseResult($data);

        return $res ? $res[0] : false;
    }

    /**
     * Returns details on a movie
     *
     * @param $tmdb_id TMDB id
     */
    function getInfo($tmdb_id)
    {
        if (!self::probeId($tmdb_id))
            throw new Exception ('not a tmdb id');

        $url = 'http://api.themoviedb.org/2.1/Movie.getInfo/en/xml/'.$this->api_key.'/'.$tmdb_id;

        $http = new HttpClient($url);
        $http->setCacheTime(60*60*24); //24 hours

        $data = $http->getBody();

        if ($http->getStatus() != 200) {
            d('TheMovieDbMetadata->getInfo server error: '.$http->getStatus() );
            d( $http->getHeaders() );
            return false;
        }

        $res = $this->parseResult($data);

        return $res ? $res[0] : false;
    }

    /** @return array of TheMovieDbMovie objects */
    private function parseResult($data)
    {
        $movies = array();

        $xml = simplexml_load_string($data);

        foreach ($xml->movies->movie as $m) {
            $movie = new TheMovieDbMovie();
            $movie->title         = $m->name.' ('.$m->alternative_name.')';
            $movie->language      = strval($m->language);
            $movie->tmdb_id       = strval($m->id);
            $movie->imdb_id       = strval($m->imdb_id);
            $movie->rating        = strval($m->rating);
            $movie->overview      = strval($m->overview);
            $movie->release_date  = strval($m->released);
            $movie->last_modified = strval($m->last_modified_at);

            if ($movie->imdb_id && !Imdb::isValidId($movie->imdb_id))
                throw new Exception ('invalid imdb id: '.$movie->imdb_id);

            foreach ($m->images->image as $i) {
                $image = new ImageResource();

                $attr = $i->attributes();

                $image->setUrl( $attr->url );
                $image->width  = strval($attr->width);
                $image->height = strval($attr->height);
                $image->image_id = strval($attr->id);
                $image->type = strval($attr->type);

                $movie->images[] = $image;
            }

            $movies[] = $movie;
        }

        return $movies;
    }

}

