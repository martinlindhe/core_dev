<?php
/**
 * $Id$
 *
 * Returns current stock quotes
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX FIXME: TempStore

//TODO: use yahoo for stock qoutes instead

//TODO: make a google finance api client: http://code.google.com/apis/finance/

require_once('StockClientWebservicex.php'); //NASDAQ only (?)
require_once('HttpClient.php');
require_once('TempStore.php');

class StockClient
{
    function getNasdaq($code)
    {
        $code = strtolower($code);

        $temp = TempStore::getInstance();
        $key = 'StockClient/nasdaq//'.$code;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);


$stock = StockClientYahoo::getNasdaq($code);

d($stock);die;

die;
        $client = new StockClientWebservicex();

        $res = $client->getQuote($code);

        if ($res)
            $temp->set($key, serialize($res), '10m');

        return $res;
    }
}


//Formatting explanation: http://www.gummy-stuff.org/Yahoo-data.htm
//TODO: api can be extended to lookup multiple stock qoutes in one call
class StockClientYahoo
{
    static function getNasdaq($code)
    {
        $format =  // result format string
        's'.  // Symbol
        'b2'. // Ask (real-time)
        'b3'. // Bid (real-time)
        'j'.  // 52-week Low
        'k';  // 52-week High

        $url = 'http://download.finance.yahoo.com/d/quotes.csv'.
        '?s='.urlencode($code).
        '&f='.$format;

        $http = new HttpClient($url);

        $data = $http->getBody();

// "AAPL",357.05,357.01,194.06,360.00

d( $data);

    }
}

?>
