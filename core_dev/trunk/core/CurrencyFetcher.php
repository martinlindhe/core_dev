<?php
/**
 * $Id$
 *
 * http://en.wikipedia.org/wiki/ISO_4217 (Standard currency codes)
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

// TODO: client for https://openexchangerates.org/, which requires paid account

namespace cd;

require_once('TempStore.php');

require_once('CurrencyFetcherGoogle.php');       // very fast, 2013-03-02
require_once('CurrencyFetcherExchangeRate.php'); // very fast, 2010-10-27
require_once('CurrencyFetcherWebservicex.php');  // very slow, 2010-10-27

class CurrencyFetcher
{
    // currency list was last updated 2009.07.23
    protected static $lookup = array(
    'AFA','ALL','AED','ARS','AWG','AUD','ANG','BSD','BHD','BDT','BBD','BZD',
    'BMD','BTN','BOB','BWP','BRL','BND','BIF','CVE','CLP','CNY','COP','CUP',
    'CYP','CZK','CRC','CAD','CHF','DKK','DJF','DOP','DZD','EGP','EEK','ETB',
    'EUR','FKP','GMD','GHC','GIP','GBP','GTQ','GNF','GYD','HTG','HNL','HKD',
    'HUF','HRK','ISK','INR','IDR','IQD','ILS','JMD','JPY','JOD','KMF','KHR',
    'KYD','KZT','KES','KRW','KWD','KPW','LAK','LVL','LBP','LSL','LRD','LYD',
    'LTL','LKR','MOP','MKD','MGF','MWK','MYR','MVR','MTL','MRO','MUR','MXN',
    'MDL','MNT','MAD','MZM','MMK','NAD','NPR','NZD','NIO','NGN','NOK','OMR',
    'PKR','PAB','PGK','PYG','PEN','PHP','PLN','QAR','ROL','RUB','STD','SAR',
    'SCR','SLL','SGD','SKK','SIT','SBD','SOS','SVC','SHP','SDD','SRG','SZL',
    'SEK','SYP','TWD','TZS','THB','TOP','TTD','TND','TRL','TRY','UGX','UAH',
    'UYU','USD','VUV','VEB','VND','WST','XPF','XPD','XPT','XAU','XCD','XOF',
    'XAF','XAG','YER','YUM','ZMK','ZWD','ZAR',
    );

    protected static function isKnownCurrency($s)
    {
        return in_array($s, self::$lookup) ? true : false;
    }

    public static function getRate($from, $to)
    {
        $from = strtoupper($from);
        $to   = strtoupper($to);

        if (!self::isKnownCurrency($from) || !self::isKnownCurrency($to))
            throw new \Exception ('unknown currency');

        $key = 'currency/'.$from.'/'.$to;
        $temp = TempStore::getInstance();

        $rate = $temp->get($key);
        if ($rate)
            return $rate;

        $rate = CurrencyFetcherGoogle::getRate($from, $to);

        $temp->set($key, $rate, '1h');
        return $rate;
    }

}
