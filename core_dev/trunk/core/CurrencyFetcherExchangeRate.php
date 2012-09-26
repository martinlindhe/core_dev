<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: set a default api key for a project email

namespace cd;

require_once('ICurrencyFetcher.php');

class CurrencyFetcherExchangeRate extends HttpClient implements ICurrencyFetcher
{
    private $api_key = 'RFJGV-fViGD-R3FGa'; //  api key for martin@startwars.org

    function setApiKey($s) { $this->api_key = $s; }

    function getRate($from, $to)
    {
        if (!$this->api_key)
            throw new Exception ('api key must be set');

        $this->setUrl('http://www.exchangerate-api.com/'.strtoupper($from).'/'.strtoupper($to).'?k='.$this->api_key);
        $res = $this->getBody();

        if ($res == '-2')
            throw new Exception ('excangerate-api.com dont support one of the currencies: '.$from.' or '.$to);

        if ($res == '-3')
            throw new Exception ('excangerate-api.com need api key, register your own at http://www.exchangerate-api.com/api-key');

        if ($res < 0)
            throw new Exception ('exchangerate-api.com returned error '.$res);

        return $res;
    }

}

?>
