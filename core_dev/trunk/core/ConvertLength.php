<?php
/**
 * $Id$
 *
 * Conversion functions for different units of length
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Length
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('ConvertBase.php');

class ConvertLength extends ConvertBase
{
    protected $scale = array( ///< unit scale to Meter
    'pm'        => 0.000000000001, //Metric: Picometer
    'nm'        => 0.000000001,    //Metric: Nanometer
    'microm'    => 0.000001,       //Metric: Micrometer
    'mm'        => 0.001,          //Metric: Millimeter
    'cm'        => 0.01,           //Metric: Centimeter
    'dm'        => 0.1,            //Metric: Decimeter
    'm'         => 1,              //Metric: Meter
    'km'        => 1000,           //Metric: Kilometer
    'scandmile' => 10000,          //Metric: Scandinavian mile, http://en.wikipedia.org/wiki/Scandinavian_mile
    'in'        => 0.0254,         //xx: Inch
    'ft'        => 0.304800610,    //xx: Feet
    'yd'        => 0.9144,         //xx: Yard
    'ukmile'    => 1852,           //UK: Mile (nautical)
    'usmile'    => 1609.344,       //US: Mile (statute)
    'au'        => 149597871464,   //xx: Astronomical Unit
    );

    protected $lookup = array(
    'picometer'    => 'pm',
    'nanometer'    => 'nm',
    'micrometer'   => 'microm',
    'millimeter'   => 'mm',
    'centimeter'   => 'cm',
    'decimeter'    => 'dm',
    'meter'        => 'm',      'meters'     => 'm',
    'kilometer'    => 'km',     'kilometers' => 'km',
    'inch'         => 'in',     'inches'     => 'in',
    'feet'         => 'ft',     'feets'      => 'ft',
    'yard'         => 'yd',     'yards'      => 'yd',
    'ukmile'       => 'ukmile',
    'usmile'       => 'usmile',
    'mile'         => 'usmile', 'miles'     => 'usmile',
    'mil'          => 'scandmile',
    'astronomical' => 'au',
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

    function convLiteral($s, $to, $from = 'meter')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
