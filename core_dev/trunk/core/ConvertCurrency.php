<?php
/**
 * $Id$
 *
 * Calculates exchange rate for currencies based on ISO 4217 currency codes
 *
 * http://en.wikipedia.org/wiki/List_of_circulating_currencies
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IConvert.php');
require_once('CurrencyFetcher.php');

class ConvertCurrency implements IConvert
{
    public static function convert($from, $to, $val)
    {
        $rate = CurrencyFetcher::getRate($from, $to, '1h');

        $scale = 20;
        return bcmul($val, $rate, $scale);
    }

}
