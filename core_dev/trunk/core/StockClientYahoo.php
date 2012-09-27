<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: cant find a official documentation of th is api

//TODO: api can be extended to lookup multiple stock qoutes in one call

namespace cd;

require_once('HttpClient.php');
require_once('CsvReader.php');

class StockClientYahoo
{
    static function getNasdaq($symbol)
    {
        $format =  // result format string
        // Cheat sheet: http://www.gummy-stuff.org/Yahoo-data.htm
        'n'.  // Name
        's'.  // Symbol
        'b2'. // Ask (real-time)
        'b3'. // Bid (real-time)
        'j'.  // 52-week Low
        'k'.  // 52-week High
        'o'.  // Open
        'p'.  // Previous close
        'h'.  // Day's high
        'g';  // Day's low

        $url = 'http://download.finance.yahoo.com/d/quotes.csv'.
        '?s='.urlencode($symbol).
        '&f='.$format;

        $http = new HttpClient($url);
        $data = $http->getBody();

        $csv = CsvReader::parse($data);

        if (count($csv) != 1)
            throw new Exception ('unhandled number of stock results: '.count($csv) );

        $stock = new StockQuoteResult();
        $stock->name           = $csv[0][0];
        $stock->symbol         = $csv[0][1];
        $stock->ask_realtime   = $csv[0][2];
        $stock->bid_realtime   = $csv[0][3];
        $stock->low_52w        = $csv[0][4];
        $stock->hi_52w         = $csv[0][5];
        $stock->open           = $csv[0][6];
        $stock->previous_close = $csv[0][7];
        $stock->day_high       = $csv[0][8];
        $stock->day_low        = $csv[0][9];

        return $stock;
    }
}

?>
