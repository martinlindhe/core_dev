<?php
/**
 * $Id$
 *
 * Get Stock quote for a company symbol
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=19
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

class StockClientWebservicex
{
    private $api_url = 'http://www.webservicex.net/stockquote.asmx?wsdl';

    function getQuote($code)
    {
        try {
            $client = new SoapClient($this->api_url);
        } catch (SoapFault $e) {
            //echo 'exception: '.$e. "\n";
            return false;
        }

        try {
            $params['symbol'] = $code;
            $val = $client->GetQuote($params);
            $data = $val->GetQuoteResult;

            $xml = simplexml_load_string($data);

            //FIXME adjust timezone of timestamp
            $timestamp = strtotime($xml->Stock->Date.' '.$xml->Stock->Time);

            $res = array(
            'Symbol'       => strval($xml->Stock->Symbol),
            'Name'         => strval($xml->Stock->Name),
            'Last'         => strval($xml->Stock->Last),
            'Timestamp'    => sql_datetime($timestamp),
            'Change'       => strval($xml->Stock->Change),
            'Open'         => strval($xml->Stock->Open),
            'High'         => strval($xml->Stock->High),
            'Low'          => strval($xml->Stock->Low),
            'Volume'       => strval($xml->Stock->Volume),
            'PreviousClose'=> strval($xml->Stock->PreviousClose),
            'PercentChange'=> strval($xml->Stock->PercentageChange),
            'Earns'        => strval($xml->Stock->Earns),
            'MktCap'       => strval($xml->Stock->MktCap),   //XXX ???
            'AnnRange'     => strval($xml->Stock->AnnRange), //XXX ???
            'PE'           => strval($xml->Stock->{'P-E'}),  //XXX ???
            );

            return $res;

        } catch (Exception $e) {
            echo 'exception: '.$e. "\n";
            echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
            echo 'Request: '.$client->__getLastRequest()."\n";
            echo 'Response: '. $client->__getLastResponse()."\n";
            return false;
        }
    }

}

?>
