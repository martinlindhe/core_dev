<?php
/**
 * $Id$
 *
 * Converts currencies using Currency Convertor web api from
 * http://www.webservicex.net/WS/WSDetails.aspx?CATID=2&WSID=10
 */

//STATUS: wip, redo into a CLASS!

function webservicex_currency_conversion_rate($from, $to)
{
    $client = new SoapClient('http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');

    try {
        $params['FromCurrency'] = strtoupper($from);
        $params['ToCurrency']   = strtoupper($to);

        $rate = $client->ConversionRate($params);
        return $rate->ConversionRateResult;

    } catch (Exception $e) {
        echo 'exception: '.$e, "\n";
        echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
        echo 'Request: '.$client->__getLastRequest()."\n";
        echo 'Response: '. $client->__getLastResponse()."\n";
        return false;
    }
}

?>
