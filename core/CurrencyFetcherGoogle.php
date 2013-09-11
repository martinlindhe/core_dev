<?php
/**
 * $Id$
 *
 * Wrapper for Google Currency Exchange data
 *
 * https://rate-exchange.appspot.com/
 *
 * @author Martin Lindhe, 2013 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('ICurrencyFetcher.php');
require_once('HttpClient.php');

class CurrencyFetcherGoogle implements ICurrencyFetcher
{
    public static function getRate($from, $to)
    {
        $url = 'http://rate-exchange.appspot.com/currency?from='.strtoupper($from).'&to='.strtoupper($to);

        $http = new HttpClient($url);

        $res = $http->getBody();
        $json = json_decode($res);

        return $json->rate;
    }

}
