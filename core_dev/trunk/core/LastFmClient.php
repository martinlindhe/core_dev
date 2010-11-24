<?php
/**
 * $Id$
 *
 * Last.fm API client for querying last.fm for music meta data
 *
 * http://www.last.fm/api/intro
 *
 */

//STATUS: wip

require_once('HttpClient.php');

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
        $http = new HttpClient('http://ws.audioscrobbler.com/2.0/?method='.$method.'&api_key='.$this->api_key);
        $http->setCacheTime(60*60*12); //12 hours

        foreach ($params as $key => $val)
            $http->Url->setParam($key, $val);

        $data = $http->getBody();
        $x = simplexml_load_string($data);

        if (isset($x->err))
            throw new Exception ( $x->err['msg'] );

        return $x;
    }

    function albumGetInfo($artist, $album)
    {
        return $this->query('album.getInfo', array('artist' => $artist, 'album' => $album) );
    }

    /**
     * @param $quality force given quality if set
     * @return url to best quality album cover for given album
     */
    function getAlbumCover($artist, $album, $quality = '')
    {
        $xml = $this->albumGetInfo($artist, $album);

        $scoring = array(
        'small'      => 1,
        'medium'     => 2,
        'large'      => 3,
        'extralarge' => 4,
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
