<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

namespace cd;

interface ICurrencyFetcher
{
    public static function getRate($from, $to);

}
