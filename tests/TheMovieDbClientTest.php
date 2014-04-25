<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('TheMovieDbClient.php');


class TheMovieDbClientTest extends \PHPUnit_Framework_TestCase
{
    static $api_key = ''; // NOTE: for tests to pass, you need an API key

    public function test1()
    {
        // NOTE: test verifies first result is the most popular "Avatar" movie

        $tmdb = new TheMovieDbClient(self::$api_key);

        $search = $tmdb->search('Avatar');

        $this->assertEquals($search->results[0]->title, "Avatar");
        $this->assertEquals($search->results[0]->id, 19995);
        $this->assertEquals($search->results[0]->release_date, "2009-12-18");
    }

    public function test2()
    {
        $tmdb = new TheMovieDbClient(self::$api_key);

        $details = $tmdb->getInfo( 19995 ); // = Avatar

        $this->assertEquals($details->imdb_id, "tt0499549");
    }

}
