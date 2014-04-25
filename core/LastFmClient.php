<?php
/**
 * Last.fm API client for querying last.fm for music meta data
 *
 * http://www.last.fm/api/intro
 *
 * @author Martin Lindhe, 2010-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('HttpClient.php');
require_once('MediaResource.php');
require_once('TempStore.php');

class LastFmClient
{
    protected $api_key = '';
    protected $language = 'en';     // ISO 639 alpha-2

    public function __construct($api_key)
    {
        if (!$api_key)
            throw new \Exception ("API key is required");

        $this->api_key = $api_key;
    }

    function setLanguage($s)
    {
        if (!in_array($s, array('en', 'sv')))
            throw new \Exception ('unknown lang: '.$s);

        $this->language = $s;
    }

    private function query($method, $params)
    {
        $url = 'http://ws.audioscrobbler.com/2.0/?method='.$method.'&api_key='.$this->api_key;

        $http = new HttpClient($url);
        $http->setCacheTime('12h');

        foreach ($params as $key => $val)
            $http->Url->setParam($key, $val);

echo "QUERYING ". $http->getUrl() ."\n";

        $data = $http->getBody();
//d($data);
        $x = simplexml_load_string($data);
/*
        $attrs = $x->attributes();
        if ($attrs['status'] == 'failed')
            throw new \Exception ('last.fm api error: '.$x->error);
*/
        return $x;
    }

    /**
     * @return info about the artist
     */
    function getArtistInfo($artist)
    {
        $temp = TempStore::getInstance();
        $key = 'LastFmClient/artist//'.$artist;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $xml = $this->query('artist.getInfo', array('artist' => $artist, 'lang' => $this->language) );

        if (isset($xml->error)) // eg: "Album not found"
            return false;

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

        $temp->set($key, serialize($artist));

        return $artist;
    }

    /**
     */
    function getAlbumCovers($artist, $album)
    {
        $temp = TempStore::getInstance();
        $key = 'LastFmClient/covers//'.$artist.'/'.$album;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $xml = $this->query('album.getInfo', array('artist' => $artist, 'album' => $album, 'lang' => $this->language) );

        if (isset($xml->error)) // eg: "Album not found"
            return false;

        $images = array();
        foreach ($xml->album->image as $i)
        {
            $attrs = $i->attributes();

            $image = new ImageResource();
            $image->type = strval($attrs['size']);
            $image->setUrl( strval($i) );
            $images[] = $image;
        }

        $temp->set($key, serialize($images));

        return $images;
    }

    /**
     * @param $quality force given quality if set
     * @return url to best quality album cover for given album
     */
    function getAlbumCover($artist, $album, $quality = 'mega')
    {
        $images = $this->getAlbumCovers($artist, $album);
        if (!$images)
            return false;

        return self::getBestImage($images, $quality);
    }

    static function getBestImage($images, $quality = 'mega')
    {
        if (!is_array($images))
            throw new \Exception ('no array given');

        $scoring = array(
        'small'      => 1,
        'medium'     => 2,
        'large'      => 3,
        'extralarge' => 4,
        'mega'       => 5,
        );

        if (!array_key_exists($quality, $scoring))
            throw new \Exception ('unrecognized quality: '.$quality);

        $score = 0;
        $best_url = '';

        foreach ($images as $i) {
            if ($i->type == $quality)
                return $i->getUrl();

            if (isset($scoring[ $i->type ])) {
                if ($scoring[ $i->type ] > $score) {
                    // echo $name. " = ".$val."\n";
                    $score = $scoring[ $i->type ];
                    $best_url = $i->getUrl();
                }
            } else
                throw new \Exception ('unknown image quality '.$i->type );
        }

        return $best_url;
    }

}
