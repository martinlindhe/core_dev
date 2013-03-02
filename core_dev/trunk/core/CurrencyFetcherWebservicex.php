<?php
/**
 * $Id$
 *
 * Converts currencies using Currency Convertor web api from
 * http://www.webservicex.net/WS/WSDetails.aspx?CATID=2&WSID=10
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

//STATUS: wip, the web service is very slow! 2010-10-03

namespace cd;

require_once('ICurrencyFetcher.php');

class CurrencyFetcherWebservicex implements ICurrencyFetcher
{
    public static function getRate($from, $to)
    {
        $client = new \SoapClient('http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');

        try {
            $params['FromCurrency'] = strtoupper($from);
            $params['ToCurrency']   = strtoupper($to);

            $rate = $client->ConversionRate($params);
            return $rate->ConversionRateResult;

        } catch (Exception $e) {
            echo "Exception: ".nl2br($e)."\n<br/>";

            return false;
        }
    }

}
