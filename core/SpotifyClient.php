<?php
/**
 * $Id$
 *
 * Spotify metadata API client
 * http://developer.spotify.com/en/metadata-api/overview/
 *
 * @author Martin Lindhe, 2009-2011 <martin@ubique.se>
 */

//STATUS: wip

//TODO: objektklasser för "artist", "record", "track"

//XXX: memcache the parsed results too, for quicker artist id lookup at least
//XXX: The rate limit is currently 10 request per second per ip. This may change.

namespace cd;

require_once('CoreBase.php');
require_once('HttpClient.php');

class SpotifyClient extends CoreBase
{
    /**
     * @param $name artist name
     * @return spotify:artist:uri or false
     */
    function getArtistId($name)
    {
        if (!$name) return false;

        $url = 'http://ws.spotify.com/search/1/artist?q='.urlencode($name);

        $http = new HttpClient($url);
        $http->setCacheTime(60*60*24); //24 hours

        $data = $http->getBody();

        //TODO: use expire time for cached response
        $expires = strtotime($http->getResponseHeader('Expires')) - time();

        if ($http->getStatus() != 200) {
            d('SpotifyMetadata->getArtistId server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        $arr = $this->parseArtists($data);

        foreach ($arr as $a) {
            if ($a['artist'] == $name) {
                //d("exact match");
                return $a['id'];
            }
            if (soundex($a['artist']) == soundex($name)) {
                //d("fuzzy match");
                return $a['id'];
            }
        }

        return false;
    }

    /**
     * @param $artist name or spotify uri
     * @param $album name
     * @return spotify:album:uri or false
     */
    function getAlbumId($artist, $album)
    {
        if (!is_spotify_uri($artist))
            $artist = $this->getArtistId($artist);

        if (!$artist)
            return false;

        $disco = $this->getArtistAlbums($artist);

        foreach ($disco as $a) {
            if ($a['album'] == $album) {
                //d("exact match");
                return $a['id'];
            }
            if (soundex($a['album']) == soundex($album)) {
                //d("fuzzy match");
                return $a['id'];
            }
        }

        return false;
    }

    /**
     * Lookup artist discography from spotify
     *
     * @param $artist_id spotify uri
     */
    function getArtistAlbums($artist_id)
    {
        if (!is_spotify_uri($artist_id))
            return false;

        $url = 'http://ws.spotify.com/lookup/1/?uri='.$artist_id.'&extras=albumdetail';

        $http = new HttpClient($url);
//        $http->setCacheTime(60*60*24); //24 hours

        $data = $http->getBody();
        if ($http->getStatus() != 200) {
            d('SpotifyMetadata->getArtistAlbums server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        return $this->parseArtistAlbums($data);
    }


    /**
     * @param $album_id spotify uri
     */
    function getAlbumDetails($album_id)
    {
        if (!is_spotify_uri($album_id))
            return false;

        $url = 'http://ws.spotify.com/lookup/1/?uri='.$album_id.'&extras=trackdetail';

        $http = new HttpClient($url);
//        $http->setCacheTime(60*60*24); //24 hours

        $data = $http->getBody();
        if ($http->getStatus() != 200) {
            d('SpotifyMetadata->getAlbumDetails server error: '.$http->getStatus() );
            d( $http->getResponseHeaders() );
            return false;
        }

        return $this->parseAlbumDetails($data);
    }

    private function parseArtists($data)
    {
        $artists = array();

        $reader = new \XMLReader();
        if ($this->getDebug()) echo 'Parsing Artists: '.$data.ln();
        $reader->xml($data);

        while ($reader->read())
        {
            if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'artists')
                break;

            if ($reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($reader->name) {
            case 'artists':
                if ($reader->getAttribute('xmlns') != 'http://www.spotify.com/ns/music/1')
                    die('XXX FIXME unsupported Spotify namespace version '.$reader->getAttribute('xmlns') );
                break;

            case 'artist':
                $artists[] = $this->parseArtist($reader);
                break;
            default:
                //TODO LATER: spotify xml exposes opensearch xml tags, see if that can be used
                //echo "unknown ".$reader->name.ln();
                break;
            }
        }

        $reader->close();
        return $artists;
    }

    private function parseAlbumDetails($data)
    {
        $tracks = array();

        $reader = new \XMLReader();
        if ($this->getDebug()) echo 'Parsing tracks: '.$data.ln();
        $reader->xml($data);

        while ($reader->read())
        {
            if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'album')
                break;

            if ($reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($reader->name) {
            case 'album':
                if ($reader->getAttribute('xmlns') != 'http://www.spotify.com/ns/music/1')
                    die('XXX FIXME unsupported Spotify namespace version '.$reader->getAttribute('xmlns') );
                break;

            case 'artist':
                $this->parseArtist($reader); //XXX store?
                break;

            case 'tracks': break;
            case 'track':
                $tracks[] = $this->parseTrack($reader);
                break;

            case 'id': break;//Album id, XXX store?
            case 'name': break;//Album name, XXX store?
            case 'released': break;//Release year, XXX store?
            case 'availability': break;
            case 'territories': break;

            default:
                //TODO LATER: spotify xml exposes opensearch xml tags, see if that can be used
                echo "parseAlbumDetails unknown ".$reader->name.ln();
                break;
            }
        }

        $reader->close();
        return $tracks;
    }

    private function parseArtistAlbums($data)
    {
        $disco = array();

        $reader = new \XMLReader();
        if ($this->getDebug()) echo 'Parsing disco: '.$data.ln();
        $reader->xml($data);

        while ($reader->read())
        {
            if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'artist')
                break;

            if ($reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($reader->name) {
            case 'name': break;
            case 'albums': break;

            case 'album':
                $id   = $reader->getAttribute('href');
                $name = '';
                $year = '';
                while ($reader->read()) {
                    if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'album') {
                        //XXX cache write aritst name + spotify id combo
                        $disco[] = array('album'=>$name, 'id'=>$id, 'year'=>$year);
                        break;
                    }

                    if ($reader->nodeType != \XMLReader::ELEMENT)
                        continue;

                    switch ($reader->name) {
                    case 'name':
                        $reader->read();
                        $name = $reader->value;
                        break;

                    case 'released':
                        $reader->read();
                        $year = $reader->value;
                        break;

                    case 'artist':
                         //XXX TODO: store & return guest artists from here
                         $tmp = $this->parseArtist($reader);
                         break;

                    case 'availability'; break;
                    case 'territories'; break;
                    case 'id': break; //XXX whats this <id type="upc">884385682460</id>
                    default: echo "bad entry (0) " .$reader->name.ln();
                    }
                }
                break;
            default:
                //TODO LATER: spotify xml exposes opensearch xml tags, see if that can be used
                //echo "unknown ".$reader->name.ln();
                break;
            }
        }

        $reader->close();
        return $disco;
    }

    private function parseArtist($reader)
    {
        $id         = $reader->getAttribute('href');
        $name       = '';
        $popularity = '';
        while ($reader->read()) {
            if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'artist') {
                //XXX cache write aritst name + spotify id combo
                return array('artist'=>$name, 'id'=>$id, 'popularity'=>$popularity);
            }

            if ($reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($reader->name) {
            case 'name':
                $reader->read();
                $name = $reader->value;
                break;
            case 'popularity':
                $reader->read();
                $popularity = $reader->value;
                break;
            default: echo "bad entry (1) " .$reader->name.ln();
            }
        }
    }

    private function parseTrack($reader)
    {
        $href   = $reader->getAttribute('href');
        $id     = '';
        $name   = '';
        $track  = '';
        $length = '';
        while ($reader->read()) {
            if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name == 'track') {
                //XXX cache write aritst name + spotify id combo
                return array('title'=>$name, 'href'=>$href, 'id'=>$id, 'track'=>$track, 'length'=>$length);
            }

            if ($reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($reader->name) {
            case 'id':
                $reader->read();
                $id = $reader->value;
                break;

            case 'name':
                $reader->read();
                $name = $reader->value;
                break;

            case 'track-number':
                $reader->read();
                $track = $reader->value;
                break;

            case 'length':
                $reader->read();
                $length = $reader->value;
                break;

            case 'artist':
                $this->parseArtist($reader); //XXX store
                break;

            case 'available': $reader->read(); break; //XXX use
            case 'popularity': $reader->read(); break; //XXX use
            case 'disc-number': $reader->read(); break; //XXX use

            default: echo "bad entry (2) " .$reader->name.ln();
            }
        }
    }

}

/**
 * Validates a Spotify uri
 * @param $uri string
 * @return true if $uri is a spotify uri
 */
function is_spotify_uri($uri)
{
    if (strpos($uri, ' ')) return false;
    $pattern = "((spotify):(album|artist|track):([a-zA-Z0-9]){22})";

    if (preg_match($pattern, $uri))
        return true;

    return false;
}

?>
