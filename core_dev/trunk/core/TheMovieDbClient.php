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

//TODO: search by opensubtitles hash

//XXX export categories, no longer in the xml?

require_once('CoreBase.php');
require_once('HttpClient.php');
require_once('Imdb.php');
require_once('MediaResource.php');
require_once('TempStore.php');

class TheMovieDbMovie
{
    var $title;
    var $language;
    var $tmdb_id;
    var $imdb_id;
    var $rating;
    var $overview;
    var $release_date;
    var $last_modified;            ///< how old is this data?
    var $images         = array(); ///< array of ImageResource objects
}

class TheMovieDbClient extends CoreBase
{
    static $api_key = '0c6598d3603824df9e50078942806320';

    static $language = 'en'; // 2 or 3-letter ISO-639 language code

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
    static function search($name)
    {
        if (!$name)
            return false;

        $temp = TempStore::getInstance();
        $key = 'TheMovieDbClient/search//'.$name;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $url = 'http://api.themoviedb.org/2.1/Movie.search/'.self::$language.'/xml/'.self::$api_key.'/'.urlencode($name);

        $http = new HttpClient($url);
        $data = $http->getBody();

        if ($http->getStatus() != 200) {
            d('TheMovieDbMetadata->search server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        $res = self::parseResult($data);
        $temp->set($key, serialize($res), '24h');

        return $res;
    }

    /**
     * Returns details on a movie
     *
     * @param $movie_id TMDB id
     */
    static function getInfo($movie_id)
    {
        if (!Imdb::isValidId($movie_id) && !self::probeId($movie_id))
            throw new Exception ('not a tmdb / imdb id');

        $temp = TempStore::getInstance();
        $key = 'TheMovieDbClient/info//'.$movie_id;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        if (Imdb::isValidId($movie_id))
            $url = 'http://api.themoviedb.org/2.1/Movie.imdbLookup/'.self::$language.'/xml/'.self::$api_key.'/'.$movie_id;
        else
            $url = 'http://api.themoviedb.org/2.1/Movie.getInfo/'.self::$language.'/xml/'.self::$api_key.'/'.$movie_id;

        $http = new HttpClient($url);
        $data = $http->getBody();

        if ($http->getStatus() != 200) {
            d('TheMovieDbMetadata->getInfo server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        $res = self::parseResult($data);
        $temp->set($key, serialize($res[0]), '24h');

        return $res ? $res[0] : false;
    }

    /** @return array of TheMovieDbMovie objects */
    private static function parseResult($data)
    {
        $movies = array();

        $xml = simplexml_load_string($data);

        foreach ($xml->movies->movie as $m) {
            $movie = new TheMovieDbMovie();
            if (strval($m->alternative_name))
                $movie->title     = strval($m->name).' ('.strval($m->alternative_name).')';
            else
                $movie->title     = strval($m->name);

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

