<?php
/**
 * $Id$
 */

interface ICurrencyFetcher
{
    public function getRate($from, $to);
}

?>
