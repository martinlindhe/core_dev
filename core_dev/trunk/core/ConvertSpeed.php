<?php
/**
 * $Id$
 *
 * Conversion functions between different speed units
 *
 * http://en.wikipedia.org/wiki/Speed
 * http://en.wikipedia.org/wiki/Miles_per_hour        = mph
 * http://en.wikipedia.org/wiki/Metre_per_second      = m/s
 * http://en.wikipedia.org/wiki/Kilometres_per_hour   = km/h
 * http://en.wikipedia.org/wiki/Knot_(unit)  = knot
 *
 * @author Martin Lindhe, 2012-2013 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertSpeed extends ConvertBase
{
    protected $scale = array( ///< unit scale to m/s
    'm/s'  => 1,         // meters per second
    'km/h' => 0.277778,  // kilometers per hour
    'ft/s' => 0.3048,    // feet per second
    'mph'  => 0.44704,   // miles per hour
    'knot' => 0.514444,
    );

    protected $lookup = array(
    );

    function conv($from, $to, $val)
    {
        $from = $this->getShortcode($from);
        $to   = $this->getShortcode($to);

        if (!$from || !$to)
            return false;

        $res = ($val * $this->scale[$from]) / $this->scale[$to];

        if ($this->precision)
            return round($res, $this->precision);

        return $res;
    }

}
