<?php
/**
 * $Id$
 *
 * Conversion functions between different speed units
 *
 * http://en.wikipedia.org/wiki/Speed
 * http://en.wikipedia.org/wiki/Miles_per_hour
 * http://en.wikipedia.org/wiki/Metre_per_second
 * http://en.wikipedia.org/wiki/Kilometres_per_hour
 * http://en.wikipedia.org/wiki/Knot_(unit)
 *
 * @author Martin Lindhe, 2012-2013 <martin@ubique.se>
 */

namespace cd;

require_once('IConvert.php');

class ConvertSpeed implements IConvert
{
    protected static $scale = array( ///< unit scale to m/s
    'm/s'  => '1',         // meters per second
    'km/h' => '0.277778',  // kilometers per hour
    'ft/s' => '0.3048',    // feet per second
    'mph'  => '0.44704',   // miles per hour
    'knot' => '0.514444',
    );

    protected static function getScale($s)
    {
        $s = trim($s);
        if (!$s)
            throw new \Exception ('no input data');

        $s = strtolower($s);
        if (!empty(self::$scale[$s]))
            return self::$scale[$s];

        throw new \Exception ('unhandled unit: '.$s);
    }

    public static function convert($from, $to, $val)
    {
        $from = self::getScale($from);
        $to   = self::getScale($to);

        $scale = 20;
        $mul = bcmul($val, $from, $scale);
        return bcdiv($mul, $to, $scale);
    }

}
