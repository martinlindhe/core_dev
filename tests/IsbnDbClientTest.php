<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IsbnDbClient.php');

class IsbnDbClientTest extends \PHPUnit_Framework_TestCase
{
    function test1()
    {
        $isbnDb = new IsbnDbClient('E2T62YHW');

        $res = $isbnDb->getByISBN('978-0-552-77429-1');

        $this->assertEquals($res->title, 'The God delusion');
        $this->assertEquals($res->authors, 'Richard Dawkins');
        $this->assertEquals($res->isbn10, '0552774294');
        $this->assertEquals($res->isbn13, '9780552774291');

    }
}
