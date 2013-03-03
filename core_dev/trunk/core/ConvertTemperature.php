<?php
/**
 * $Id$
 *
 * Conversion functions for different units of temperature
 *
 * http://en.wikipedia.org/wiki/Temperature_conversion_formulas
 * http://en.wikipedia.org/wiki/Conversion_of_units#Temperature
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IConvert.php');

class ConvertTemperature implements IConvert
{
    protected static $units = array(
    'celcius'    => 0,
    'fahrenheit' => 0,
    'rakine'     => 0,
    'kelvin'     => 0,
    // TODO: milliKelvin
    );

    protected static $lookup = array(
    'c'        => 'celcius',
    'f'        => 'fahrenheit',
    'r'        => 'rakine',
    'k'        => 'kelvin',
    );

    protected static function getShortcode($s)
    {
        $s = trim($s);
        if (!$s)
            throw new \Exception ('no input data');

        $s = strtolower($s);

        if (isset(self::$units[$s]))
            return $s;

        if (isset(self::$lookup[$s]))
            return self::$lookup[$s];

        throw new \Exception ('unhandled unit: '.$s);
    }

    public static function convert($from, $to, $val)
    {
        $from = self::getShortcode($from);
        $to   = self::getShortcode($to);

        //convert to celcius for internal representation
        switch (strtolower($from)) {
        case 'celcius': $cel =  $val; break;
        case 'fahrenheit': $cel = ($val - 32) * (5/9); break;
        case 'rakine': $cel = ($val - 491.67) * (5/9); break;
        case 'kelvin': $cel =  $val - 273.15; break;
        default: throw new \Exception ('from val: '.$from);
        }

        switch (strtolower($to)) {
        case 'celcius': $res =  $cel; break;
        case 'fahrenheit': $res = ($cel * (9/5)) + 32; break;
        case 'rakine': $res = ($cel + 273.15) * (9/5); break;
        case 'kelvin': $res =  $cel + 273.15; break;
        default: throw new \Exception ('to val: '.$to);
        }

        return $res;
    }

}
