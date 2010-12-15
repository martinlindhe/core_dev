<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: convert input to standard presentation
//TODO: output format renderers

class Coordinate
{
    var $latitude;  // 59.332169 = stockholm,sweden
    var $longitude; // 18.062429 = stockholm,sweden

    function __construct($lat, $long)
    {
        $this->latitude  = $lat;
        $this->longitude = $long;
    }

}

?>
