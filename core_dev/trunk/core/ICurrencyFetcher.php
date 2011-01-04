<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

interface ICurrencyFetcher
{
    public function getRate($from, $to);
}

?>
