<?php
/**
 * $Id$
 *
 * Last.fm API client for querying last.fm for music meta data
 *
 * http://www.last.fm/api/intro
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: getArtistInfo() parse "similar" artists tag

require_once('HttpClient.php');
require_once('MediaResource.php');

class ArtistResource
{
    var $name;
    var $musicbrainz_id;
    var $summary;          ///< quick summary of artist
    var $detailed_info;    ///< more details of artist
    var $images = array(); ///< array of ImageResource objects
    var $tags   = array(); ///< array of strings, "tags" such as music genre
}

class LastFmClient
{
    static $_instance; ///< singleton

    protected $api_key = 'b25b959554ed76058ac220b7b2e0a026'; // from last.fm api doc

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setApiKey($s) { $this->api_key = $s; }

    private function query($method, $params)
    {
        $url = 'http://ws.audioscrobbler.com/2.0/?method='.$method.'&api_key='.$this->api_key;

        $http = new HttpClient($url);
        $http->setCacheTime('12h');

        foreach ($params as $key => $val)
            $http->Url->setParam($key, $val);

//d( $http->getUrl() );

        $data = $http->getBody();
        $x = simplexml_load_string($data);

        return $x;
    }

    /**
     * @return info about the artist
     */
    function getArtistInfo($artist)
    {
        $xml = $this->query('artist.getInfo', array('artist' => $artist) );

        $artist = new ArtistResource();
        $artist->name           = strval($xml->artist->name);
        $artist->musicbrainz_id = strval($xml->artist->mbid);
        $artist->summary        = strval($xml->artist->bio->summary);
        $artist->detailed_info  = strval($xml->artist->bio->content);

        foreach ($xml->artist->image as $i) {
            $image = new ImageResource();

            $attr = $i->attributes();

            $image->setUrl( strval($i) );
            $image->type = strval($attr->size);

            $artist->images[] = $image;
        }

        foreach ($xml->artist->tags->tag as $t)
            $artist->tags[] = strval($t->name);

        return $artist;
    }

    /**
     * @param $quality force given quality if set
     * @return url to best quality album cover for given album
     */
    function getAlbumCover($artist, $album, $quality = '')
    {
        $xml = $this->query('album.getInfo', array('artist' => $artist, 'album' => $album) );

        if (isset($xml->error)) // eg: "Album not found"
            return false;

        $scoring = array(
        'small'      => 1,
        'medium'     => 2,
        'large'      => 3,
        'extralarge' => 4,
        'mega'       => 5,
        );

        $score = 0;
        $best_url = '';

        foreach ($xml->album->image as $i)
            foreach ($i->attributes() as $name => $val)
                if ($name == 'size') {
                    $val = strval($val);

                    if ($quality && $val == $quality)
                        return strval($i);

                    if (isset($scoring[ $val ])) {
                        if ($scoring[ $val ] > $score) {
                            // echo $name. " = ".$val."\n";
                            $score = $scoring[ $val ];
                            $best_url = strval($i);
                        }
                    } else
                        throw new Exception ('unknown image quality '.$val);
                }

        return $best_url;
    }

}

?>
