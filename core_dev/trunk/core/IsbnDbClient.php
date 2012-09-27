<?php
/**
 * $Id$
 *
 * API client for http://isbndb.com/ (ISBN book search api)
 *
 * API documentation:
 * https://isbndb.com/docs/api/
 *
 * Must register for an api key:
 * https://isbndb.com/account/create.html
 *
 */

//STATUS: wip

//TODO: add api usage "keystats" method, see https://isbndb.com/docs/api/40-common.html for example

namespace cd;

require_once('HttpClient.php');
require_once('ISBN.php');
require_once('MediaResource.php'); // for BookResource
require_once('TempStore.php');

class IsbnDbClient
{
    private $api_key;

    function setApiKey($s) { $this->api_key = $s; }

    function getByISBN($isbn)
    {
        if (!ISBN::isValid($isbn))
            throw new \Exception ('invalid isbn');

        if (!$this->api_key)
            throw new \Exception ('api key required');

        $temp = TempStore::getInstance();

        $key = 'IsbnDbClient/isbn/'.$isbn;
        $res = $temp->get($key);

        if ($res)
            return unserialize($res);

        $url = 'http://isbndb.com/api/books.xml?access_key='.$this->api_key.'&index1=isbn&value1='.$isbn;
        $http = new HttpClient($url);
        $http->setCacheTime('4h');
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

        $temp->set($key, serialize($book), '24h');

        return $book;
    }

}

?>
