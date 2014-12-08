<?php

namespace cd;

class SpotifyClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertEquals(is_spotify_uri('spotify:album:5fMriFQESKP2AWddR4jypS'), true);
    }
    public function test2()
    {
        $this->assertEquals(is_spotify_uri('spotify:artist:4YrKBkKSVeqDamzBPWVnSJ'), true);
    }
    public function test3()
    {
        $this->assertEquals(is_spotify_uri('spotify:track:3zBhJBEbDD4a4SO1EaEiBP'), true);
    }
    public function test4()
    {
        $this->assertEquals(is_spotify_uri('spotify:album:'), false);
    }
    public function test5()
    {
        $this->assertEquals(is_spotify_uri('spotify:album:so'), false);
    }
    public function test6()
    {
        $this->assertEquals(is_spotify_uri('spotify::so'), false);
    }
    public function test7()
    {
        $this->assertEquals(is_spotify_uri('spotify:xx:so'), false);
    }
    public function test8()
    {
        $this->assertEquals(is_spotify_uri('xxx:xxx:xxxx'), false);
    }
    public function test9()
    {
        $this->assertEquals(is_spotify_uri('xxx'), false);
    }
    public function test10()
    {
        $this->assertEquals(is_spotify_uri('::'), false);
    }

    public function testGetArtistId()
    {
        $artist = 'Mohammed Ali';

        $spotify = new SpotifyClient();
        $artist_id = $spotify->getArtistId($artist);

        $this->assertEquals($artist_id, 'spotify:artist:0PwLAFedFpxos2G2tlZ2ES');
    }

    public function testGetAlbumId()
    {
        $artist_id = 'spotify:artist:0PwLAFedFpxos2G2tlZ2ES'; // Mohammed Ali
        $album = 'Processen';

        $spotify = new SpotifyClient();
        $album_id = $spotify->getAlbumId($artist_id, $album);

        $this->assertEquals($album_id, 'spotify:album:5fMriFQESKP2AWddR4jypS');
    }

    public function testAlbumDetails()
    {
        $album_id = 'spotify:album:5fMriFQESKP2AWddR4jypS'; // Processen

        $spotify = new SpotifyClient();
        $tracks = $spotify->getAlbumDetails($album_id);
        $this->assertEquals(count($tracks), 11);

        $this->assertEquals($tracks[0]['title'], 'Lever För Det Här');
        $this->assertEquals($tracks[0]['href'], 'spotify:track:41dtyTOUAqSQFtgCLJuyL6');
        $this->assertEquals($tracks[0]['id'], 'SEVQI0900601');
        $this->assertEquals($tracks[0]['track'], 1);
        $this->assertEquals($tracks[0]['length'], 201.133000);
    }
}
