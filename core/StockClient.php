<?php
/**
 * $Id$
 *
 * Returns current stock quotes
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO later: also make a google finance api client: http://code.google.com/apis/finance/

namespace cd;

require_once('StockClientYahoo.php'); //NASDAQ only (?)
require_once('TempStore.php');

class StockQuoteResult
{
    var $name;
    var $symbol;
    var $ask_realtime;
    var $bid_realtime;
    var $low_52w;         ///< 52-week Low
    var $hi_52w;          ///< 52-week High
    var $open;
    var $previous_close;
    var $day_high;
    var $day_low;
}

class StockClient
{
    /** @return a StockQuoteResult object */
    static function getNasdaq($symbol)
    {
        $code = strtolower($symbol);

        $temp = TempStore::getInstance();
        $key = 'StockClient/nasdaq//'.$symbol;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $res = StockClientYahoo::getNasdaq($symbol);

        if ($res)
            $temp->set($key, serialize($res), '10m');

        return $res;
    }
}

?>
