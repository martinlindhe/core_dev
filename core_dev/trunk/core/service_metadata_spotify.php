<?php
/**
 * $Id$
 *
 * Spotify metadata api
 * http://developer.spotify.com/en/metadata-api/overview/
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */
require_once('class.CoreBase.php');

//STATUS: incomplete wip

//XXX BUG: getArtistId returnerar ett artist_id men sen om man använder det id't för att hämta "album" data så får man 400-error

//XXX: memcache the parsed results too, for quicker artist id lookup at least
//XXX: The rate limit is currently 10 request per second per ip. This may change.

class SpotifyMetadata extends CoreBase
{
	/**
	 * @param $name artist name
	 * @return spotify:artist:uri or false
	 */
	function getArtistId($name)
	{
		if (!$name) return false;

		$url = 'http://ws.spotify.com/search/1/artist?q='.urlencode($name);

		$u = new HttpClient($url);
		$u->setCacheTime(60*60*48); //48 hours

		$data = $u->getBody();
		if ($u->getStatus() != 200) {
			d('SpotifyMetadata server error: '.$u->getStatus() );
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
		$url = 'http://ws.spotify.com/lookup/1/?uri='.$artist_id.'&extras=albumdetail';

		$u = new HttpClient($url);
		$u->setCacheTime(60*60*48); //48 hours

		$data = $u->getBody();
		if ($u->getStatus() != 200) {
			d('SpotifyMetadata server error: '.$u->getStatus() );
			return false;
		}

		return $this->parseArtistAlbums($data);
	}

	private function parseArtists($data)
	{
		$artists = array();

		$reader = new XMLReader();
		if ($this->debug) echo 'Parsing Artists: '.$data.ln();
		$reader->xml($data);

		$item = new MediaItem();

		while ($reader->read())
		{
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'artists')
				break;

			if ($reader->nodeType != XMLReader::ELEMENT)
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

	private function parseArtist($reader)
	{
		$id         = $reader->getAttribute('href');
		$name       = '';
		$popularity = '';
		while ($reader->read()) {
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'artist') {
				//XXX cache write aritst name + spotify id combo
				return array('artist'=>$name, 'id'=>$id, 'popularity'=>$popularity);
			}

			if ($reader->nodeType != XMLReader::ELEMENT)
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
			default: echo "bad entry " .$reader->name.ln();
			}
		}
	}

	private function parseArtistAlbums($data)
	{
		$disco = array();

		$reader = new XMLReader();
		if ($this->debug) echo 'Parsing disco: '.$data.ln();
		$reader->xml($data);

		$item = new MediaItem();

		while ($reader->read())
		{
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'artist')
				break;

			if ($reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($reader->name) {
			case 'name': break;
			case 'albums': break;

			case 'album':
				$id   = $reader->getAttribute('href');
				$name = '';
				$year = '';
				while ($reader->read()) {
					if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'album') {
						//XXX cache write aritst name + spotify id combo
						$disco[] = array('album'=>$name, 'id'=>$id, 'year'=>$year);
						break;
					}

					if ($reader->nodeType != XMLReader::ELEMENT)
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
					default: echo "bad entry " .$reader->name.ln();
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

}

/**
 * Validates a Spotify uri
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
