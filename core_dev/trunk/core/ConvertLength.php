<?php
/**
 * $Id$
 *
 * Conversion functions for different units of length
 *
 * http://en.wikipedia.org/wiki/Conversion_of_units#Length
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertLength extends ConvertBase
{
    protected $scale = array( ///< unit scale to Meter
    'ym'        => 0.000000000000000000000001, // SI: Yoctometre     10^−24m
    'zm'        => 0.000000000000000000001,    // SI: Zeptometre     10^−21m
    'am'        => 0.000000000000000001,       // SI: Attometre      10^−18m
    'fm'        => 0.000000000000001,          // SI: Femtometre     10^−15m
    'pm'        => 0.000000000001,             // SI: Picometre      10^−12m
    'nm'        => 0.000000001,                // SI: Nanometre      10^-9m
    'microm'    => 0.000001,                   // SI: Micrometre, μm 10^-6m
    'mm'        => 0.001,                      // SI: Millimetre     10^-3m
    'cm'        => 0.01,                       // SI: Centimetre     10^-2m
    'dm'        => 0.1,                        // SI: Decimetre      10^-1m
    'm'         => 1,                          // SI: Meter
    'km'        => 1000,                       // SI: Kilometer      10^3m
    'scandmile' => 10000,                      // Scandinavian mile, http://en.wikipedia.org/wiki/Scandinavian_mile
    'in'        => 0.0254,                     // XXX: Inch
    'ft'        => 0.304800610,                // XXX: Feet
    'yd'        => 0.9144,                     // XXX: Yard
    'ukmile'    => 1852,                       // UK: Mile (nautical)
    'usmile'    => 1609.344,                   // US: Mile (statute)
    'ld'        => 384400000,                  // Lunar distance, http://en.wikipedia.org/wiki/Lunar_distance_%28astronomy%29
    'au'        => 149597870700,               // Astronomical Unit
    );

    protected $lookup = array(
    'yoctometer'   => 'ym',     'yoctometre'   => 'ym',
    'zeptometer'   => 'zm',     'zeptometre'   => 'zm',
    'attometer'    => 'am',     'attometre'    => 'am',
    'femtometer'   => 'fm',     'femtometre'   => 'fm',
    'picometer'    => 'pm',     'picometre'  => 'pm',
    'nanometer'    => 'nm',     'nanometre'  => 'nm',
    'micrometer'   => 'microm', 'micrometre' => 'microm', 'µm' => 'microm', 'micron' => 'microm', 'microns' => 'microm',
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
    'lunar'        => 'ld',
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
