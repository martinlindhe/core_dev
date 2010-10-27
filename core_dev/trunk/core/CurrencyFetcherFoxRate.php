<?php
/**
 * $Id$
 */

//STATUS: wip

require_once('XmlRpcClient.php');

require_once('ICurrencyFetcher.php');

class CurrencyFetcherFoxRate extends XmlRpcClient implements ICurrencyFetcher
{
    function getRate($from, $to)
    {
        $this->setUrl('http://foxrate.org/rpc/');
        $res = $this->call('foxrate.currencyConvert', array($from, $to, 1) );
        if ($res['flerror'] != '0') {
            d($res);
            throw new Exception ('foxrate.currencyConvert error: '. $res['flerror']);
        }

        return $res['amount'];
    }

}

?>
