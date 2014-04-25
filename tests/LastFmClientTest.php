<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('LastFmClient.php');

class LastFmClientTest extends \PHPUnit_Framework_TestCase
{
    static $api_key = ""; // NOTE: for test to pass, you need a valid api key

    function test1()
    {
        $client = new LastFmClient(self::$api_key);

        $res = $client->getAlbumCover('Eminem', 'Encore');
        $this->assertEquals($res, 'http://userserve-ak.last.fm/serve/_/88163529/Encore.png');
    }

    function test2()
    {
        $client = new LastFmClient(self::$api_key);

        $res = $client->getArtistInfo('Eminem');

        $this->assertEquals($res->name, 'Eminem');
        $this->assertEquals($res->musicbrainz_id, 'b95ce3ff-3d05-4e87-9e01-c97b66af13d4');
    }

    function test3()
    {
        $client = new LastFmClient(self::$api_key);

        $res = $client->getArtistInfo('Eminem');
        $img = LastFmClient::getBestImage( $res->images );

        $this->assertEquals($img, 'http://userserve-ak.last.fm/serve/500/94395715/Eminem+Digital+Booklet++The+Marshall.png');
    }

}
