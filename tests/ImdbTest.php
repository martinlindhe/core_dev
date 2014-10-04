<?php

namespace cd;

class ImdbTest extends \PHPUnit_Framework_TestCase
{
    public function test_isValidId()
    {
        $this->assertEquals(Imdb::isValidId('tt0499549'), true);
    }

    public function test_is_imdb_url()
    {
        $this->assertEquals(is_imdb_url('http://www.imdb.com/title/tt1837642/'), true);
    }

    public function test_getIdFromUrl()
    {
        $this->assertEquals(Imdb::getIdFromUrl('http://www.imdb.com/title/tt1837642/'), 'tt1837642');
    }
}
