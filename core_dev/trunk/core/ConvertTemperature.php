<?php
/**
 * $Id$
 *
 * Conversion functions for different units of temperature
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Temperature_conversion_formulas
 * http://en.wikipedia.org/wiki/Conversion_of_units#Temperature
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertTemperature extends ConvertBase
{
    protected $lookup = array(
    'celcius'    => 'c',
    'fahrenheit' => 'f',
    'rakine'     => 'r',
    'kelvin'     => 'k',
    );

    function conv($from, $to, $val)
    {
        $from = $this->getShortcode($from);
        $to   = $this->getShortcode($to);

        if (!$from || !$to)
            return false;

        //convert to celcius for internal representation
        switch (strtolower($from)) {
        case 'c': $cel =  $val; break;
        case 'f': $cel = ($val - 32) * (5/9); break;
        case 'r': $cel = ($val - 491.67) * (5/9); break;
        case 'k': $cel =  $val - 273.15; break;
        default: return false;
        }

        switch (strtolower($to)) {
        case 'c': $res =  $cel; break;
        case 'f': $res = ($cel * (9/5)) + 32; break;
        case 'r': $res = ($cel + 273.15) * (9/5); break;
        case 'k': $res =  $cel + 273.15; break;
        default: return false;
        }

        if ($this->precision)
            return round($res, $this->precision);

        return $res;
    }

    function convLiteral($s, $to, $from = 'celcius')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
