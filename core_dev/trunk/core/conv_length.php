<?php
/**
 * $Id$
 *
 * Conversion functions for different units of length
 *
 * Most countries uses the metric system except UK and the US.
 *
 * http://en.wikipedia.org/wiki/Metrication_in_the_United_Kingdom
 * http://en.wikipedia.org/wiki/Metrication_in_the_United_States
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Length
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('class.CoreConverter.php');

class ConvertLength extends CoreConverter
{
    protected $scale = array( ///< unit scale to Meter
    'pm'     => 0.000000000001, //Picometer
    'nm'     => 0.000000001,    //Nanometer
    'mm'     => 0.001,          //Millimeter
    'cm'     => 0.01,           //Centimeter
    'dm'     => 0.1,            //Decimeter
    'm'      => 1,              //Meter
    'km'     => 1000,           //Kilometer
    'in'     => 0.0254,         //Inch
    'ft'     => 0.304800610,    //Feet
    'yd'     => 0.9144,         //Yard
    'ukmile' => 1852,           //UK nautical mile
    'usmile' => 1609.344,       //US statute mile
    'au'     => 149597871464    //Astronomical Unit
    );

    protected $lookup = array(
    'picometer'  => 'pm',
    'nanometer'  => 'nm',
    'millimeter' => 'mm',
    'centimeter' => 'cm',
    'decimeter'  => 'dm',
    'meter'      => 'm',
    'kilometer'  => 'km',
    'inch'       => 'in',
    'feet'       => 'ft',
    'yard'       => 'yd',
    'ukmile'     => 'ukmile',
    'usmile'     => 'usmile',
    'mile'       => 'usmile',
    'astronomical'=>'au',
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

?>
