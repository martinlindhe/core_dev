<?php
/**
 * $Id$
 */

abstract class CurrencyFetcherBase
{
    abstract function getRate($from, $to);
}

?>
