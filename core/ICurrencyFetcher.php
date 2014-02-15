<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

namespace cd;

interface ICurrencyFetcher
{
    public static function getRate($from, $to);

}
