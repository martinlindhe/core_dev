<?php
/**
 * $Id$
 */

//STATUS: wip

require_once('ICurrencyFetcher.php');

class CurrencyFetcherExchangeRate implements ICurrencyFetcher
{
    private $api_key = ''; // (free) register for one at http://www.exchangerate-api.com/api-key

    function __construct($api_key = '')
    {
        if ($api_key)
            $this->api_key = $api_key;
    }

    function getRate($from, $to)
    {
        if (!$this->api_key)
            throw new Exception ('api key must be set');

        $http = new HttpClient('http://www.exchangerate-api.com/'.strtoupper($from).'/'.strtoupper($to).'?k='.$this->api_key);
        $res = $http->getBody();

        if ($res < 0)
            throw new Exception ('exchangerate-api.com returned error '.$res);

        return $res;
    }

}

?>
