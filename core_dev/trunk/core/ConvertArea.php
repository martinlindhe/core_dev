<?php
/**
 * $Id$
 *
 * Conversion functions between different units of area
 *
 * http://en.wikipedia.org/wiki/Area
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IConvert.php');

class ConvertArea implements IConvert
{
    protected static $scale = array( ///< unit scale to square metre (m²)
    'mm²'   => 0.000001,       // square millimetre (1.0 × 10^-6 m²)
    'cm²'   => 0.0001,         // square centimetre
    'dm²'   => 0.01,           // square decimetre
    'm²'    => 1,              // square metre
    'a'     => 100,            // are (100 m²)
    'ha'    => 10000,          // hectare (10 000 m²)
    'km²'   => 1000000,        // square kilometre (100 hectares)
    'in²'   => 0.00064516,     // square inch
    'ft²'   => 0.09290304,     // square foot = 144 square inches
    'yd²'   => 0.83612736,     // square yard = 9 square feet
    'acre'  => 4046.8564224,   // 1 acre = 4840 square yards = 43560 square feet
    'mile²' => 2589988.11,     // square mile (U.S. mile) = 640 acres
    );

    protected static $lookup = array(
    'square millimeter'=> 'mm²',   'square millimeters'=> 'mm²',
    'square millimetre'=> 'mm²',   'square millimetres'=> 'mm²',
    'square centimeter'=> 'cm²',   'square centimeters'=> 'cm²',
    'square centimetre'=> 'cm²',   'square centimetres'=> 'cm²',
    'square decimeter' => 'dm²',   'square decimeters' => 'dm²',
    'square decimetre' => 'dm²',   'square decimetres' => 'dm²',
    'square meter'     => 'm²',    'square meters'     => 'm²',
    'square metre'     => 'm²',    'square metres'     => 'm²',
    'are'              => 'a',     'ares'              => 'a',
    'hectare'          => 'ha',    'hectares'          => 'ha',
    'square kilometer' => 'km²',   'square kilometers' => 'km²',
    'square kilometre' => 'km²',   'square kilometres' => 'km²',
    'square inch'      => 'in²',   'square inches'     => 'in²',
    'square foot'      => 'ft²',   'square feet'       => 'ft²',
    'square yard'      => 'yd²',   'square yards'      => 'yd²',
    'square mile'      => 'mile²', 'square miles'      => 'mile²',
    'acres'            => 'acre',
    );

    protected static function getScale($s)
    {
        $s = trim($s);
        if (!$s)
            throw new \Exception ('no input data');

        if (!empty(self::$lookup[$s]))
            return self::$scale[ self::$lookup[$s] ];

        if (in_array($s, self::$lookup) || array_key_exists($s, self::$lookup))
            return self::$scale[$s];

        if (!empty(self::$scale[$s]))
            return self::$scale[$s];

        throw new \Exception (get_class($this).': unhandled unit: '.$s);
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
