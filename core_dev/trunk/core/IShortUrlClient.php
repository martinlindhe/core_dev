<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: goo.gl client
//TODO: bit.ly client

require_once('ShortUrlClientIsGd.php');
require_once('ShortUrlClientTinyUrl.php');

interface IShortUrlClient
{
    static function getShortUrl($input_url);
}

?>
