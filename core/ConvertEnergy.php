<?php
/**
 * $Id$
 *
 * Conversion functions between different units of energy
 *
 * http://en.wikipedia.org/wiki/Conversion_of_units#Energy
 * http://en.wikipedia.org/wiki/Unit_of_energy
 * http://en.wikipedia.org/wiki/Kilowatt_hour
 *
 * @author Martin Lindhe, 2012-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IConvert.php');

class ConvertEnergy implements IConvert
{
    protected static $scale = array( ///< unit scale to watt hour
    'microwatt_h' => '0,000001',         // microwatt hour 10^-6
    'milliwatt_h' => '0,001',            // milliwatt hour 10^-3
    'wh'          => '1',                // watt hour
    'kilowatt_h'  => '1000',             // kilowatt hour, 10^3
    'megawatt_h'  => '1000000',          // megawatt hour  10^6
    'gigawatt_h'  => '1000000000',       // gigawatt hour  10^9
    'terawatt_h'  => '1000000000000',    // terawatt hour  10^12
    'petawatt_h'  => '1000000000000000', // petawatt hour  10^15
    );

    protected static $lookup = array(
    'µwh'   => 'microwatt_h',
    'mwh'   => 'milliwatt_h',
    'kwh'   => 'kilowatt_h',
    'mwh'   => 'megawatt_h',
    'gwh'   => 'gigawatt_h',
    'twh'   => 'terawatt_h',
    'pwh'   => 'petawatt_h',
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
