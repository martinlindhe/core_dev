<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('ShortUrlClientBitLy.php');
require_once('ShortUrlClientGooGl.php');
require_once('ShortUrlClientIsGd.php');
require_once('ShortUrlClientTinyUrl.php');

interface IShortUrlClient
{
    /**
     * Creates a short URL from input URL
     *
     * @param $input_url input URL
     * @return short URL or false on error
     */
    static function shorten($input_url);
}

?>
