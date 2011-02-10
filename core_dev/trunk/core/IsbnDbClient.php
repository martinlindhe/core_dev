<?php
/**
 * $Id$
 *
 * API client for http://isbndb.com/ (ISBN book search api)
 *
 * Must register for an api key:
 * https://isbndb.com/account/create.html
 *
 */

//STATUS: wip

//TODO: add api usage "keystats" method, see https://isbndb.com/docs/api/40-common.html for example

require_once('HttpClient.php');
require_once('ISBN.php');
require_once('MediaResource.php'); // for BookResource

class IsbnDbClient
{
    private $api_key;

    function setApiKey($s) { $this->api_key = $s; }

    function getByISBN($isbn)
    {
        if (!ISBN::isValid($isbn))
            throw new Exception ('invalid isbn');

        $url = 'http://isbndb.com/api/books.xml?access_key='.$this->api_key.'&index1=isbn&value1='.$isbn;
        $http = new HttpClient($url);
        $http->setCacheTime('4h');
        $data = $http->getBody();

        $xml = simplexml_load_string($data);

        $d = $xml->BookList->BookData;
        $attrs = $d->attributes();

        $book = new BookResource();
        $book->title     = strval($d->Title);
        $book->authors   = strval($d->AuthorsText);
        $book->publisher = strval($d->PublisherText);
        $book->isbn10    = strval($attrs['isbn']);
        $book->isbn13    = strval($attrs['isbn13']);

        return $book;
    }

}

?>
