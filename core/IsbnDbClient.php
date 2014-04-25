<?php
/**
 * API client for http://isbndb.com/ (ISBN book search api)
 *
 * API documentation:
 * https://isbndb.com/docs/api/
 *
 * Must register for an api key:
 * https://isbndb.com/account/create.html
 *
 */

//STATUS: wip, API works as of 2014-04-25

namespace cd;

require_once('HttpClient.php');
require_once('Isbn.php');
require_once('MediaResource.php'); // for BookResource
require_once('TempStore.php');

class IsbnDbClient
{
    private $api_key;

    public $use_cache = false;

    public function __construct($api_key)
    {
        if (!$api_key)
            throw new \Exception ("API key is required");

        $this->api_key = $api_key;
    }

    function getByISBN($isbn)
    {
        if (!Isbn::isValid($isbn))
            throw new \Exception ('invalid isbn');

        $isbn = str_replace(' ', '', $isbn);
        $isbn = str_replace('-', '', $isbn);

        if ($this->use_cache) {
            $temp = TempStore::getInstance();

            $key = 'IsbnDbClient/isbn/'.$isbn;
            $res = $temp->get($key);

            if ($res)
                return unserialize($res);
        }

        $url =
        'http://isbndb.com/api/books.xml'.
        '?access_key='.$this->api_key.
        '&index1=isbn'.
        '&value1='.$isbn;

        $http = new HttpClient($url);
        $data = $http->getBody();

        $xml = simplexml_load_string($data);

        $attrs = $xml->BookList;
        if ($attrs['total_results'] == 0)
            return false;

        $d = $xml->BookList->BookData;
        $attrs = $d->attributes();
        if (!$attrs)
            throw new \Exception ('no attrs');

        $book = new BookResource();
        $book->title     = strval($d->Title);
        $book->authors   = strval($d->AuthorsText);
        $book->publisher = strval($d->PublisherText);
        $book->isbn10    = strval($attrs['isbn']);
        $book->isbn13    = strval($attrs['isbn13']);

        if ($this->use_cache) {
            $temp->set($key, serialize($book), '24h');
        }

        return $book;
    }

}
