<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

//STATUS: wip

//TODO: set a default api key for a project email

namespace cd;

require_once('ICurrencyFetcher.php');
require_once('HttpClient.php');

class CurrencyFetcherExchangeRate implements ICurrencyFetcher
{
    public static function getRate($from, $to)
    {
        $api_key = 'RFJGV-fViGD-R3FGa'; //  api key for martin@ubique.se

        $url = 'http://www.exchangerate-api.com/'.strtoupper($from).'/'.strtoupper($to).'?k='.$api_key;

        $http = new HttpClient($url);

        $res = $http->getBody();

        if ($res == '-2')
            throw new \Exception ('unsupported currency:'.$from.' or '.$to);

        if ($res == '-3')
            throw new \Exception ('need api key, register your own at http://www.exchangerate-api.com/api-key');

        if ($res < 0)
            throw new \Exception ('error '.$res);

        return $res;
    }

}
