<?php
/**
 * $Id$
 *
 * Conversion functions between different units of mass
 *
 * http://en.wikipedia.org/wiki/Conversion_of_units#Mass
 *
 * @author Martin Lindhe, 2009-2013 <martin@ubique.se>
 */

namespace cd;

require_once('IConvert.php');

class ConvertMass implements IConvert
{
    protected static $scale = array( ///< unit scale to Gram
    'g'  => '1',              // Gram
    'hg' => '100',            // Hectogram
    'kg' => '1000',           // Kilogram
    't'  => '1000000',        // Metric tonne
    'kt' => '1000000000',     // Kilotonne
    'mt' => '1000000000000',  // Megatonne
    'oz' => '28.349523125',   // Ounce
    'lb' => '453.59237',      // Pound = 16 ounces
    'st' => '6350.29318',     // Stone = 14 pounds
    );

    protected static $lookup = array(
    'gram'      => 'g',
    'hecto'     => 'hg', 'hectogram' => 'hg',
    'kilo'      => 'kg', 'kilogram'  => 'kg',
    'ton'       => 't',  'tonne'     => 't',
    'kiloton'   => 'kt', 'kilotonne' => 'kt',
    'megaton'   => 'mt', 'megatonne' => 'mt',
    'ounce'     => 'oz',    'ounces' => 'oz',
    'pound'     => 'lb',    'pounds' => 'lb', 'lbs'  => 'lb',
    'stone'     => 'st',    'stones' => 'st',
    );

    protected static function getScale($s)
    {
        $s = trim($s);
        if (!$s)
            throw new \Exception ('no input data');

        $s = strtolower($s);
        if (!empty(self::$lookup[$s]))
            return self::$scale[ self::$lookup[$s] ];

        if (!empty(self::$scale[$s]))
            return self::$scale[$s];

        $s = strtoupper($s);
        if (!empty(self::$lookup[$s]))
            return self::$scale[ self::$lookup[$s] ];

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
