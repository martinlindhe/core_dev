<?php
/**
 * $Id$
 *
 * Conversion functions between different units of volume
 *
 * https://en.wikipedia.org/wiki/Litre
 * http://en.wikipedia.org/wiki/Volume
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

namespace cd;

require_once('IConvert.php');

class ConvertVolume implements IConvert
{
    protected static $scale = array( ///< unit scale to 1 liter
//    'yl'         => 0,            // yoctolitre 10 ^−24 L
//    'zl'         => 0,            // zeptolitre 10 ^−21 L
//    'al'         => 0,            // attolitre 10 ^−18 L
//    'fl'         => 0,            // femtolitre 10 ^−15 L
//    'pl'         => 0,            // picolitre 10 ^−12 L
//    'nl'         => 0,            // nanolitre 10 ^−9 L
    'ml'         => '0.001',        // milliliter  10 ^−3 L
    'cl'         => '0.01',         // centiliter  10 ^−2 L
    'dl'         => '0.1',          // deciliter   10 ^−1 L
    'l'          => '1',            // liter       10 ^0 L
    'dal'        => '10',           // decalitre 10 ^1 L
    'hl'         => '100',          // hectolitre 10 ^2 L
    'kl'         => '1000',         // kilolitre  10 ^3 L
    'ML'         => '1000000',      // megalitre 10 ^6 L
//    'GL'         => 0,            // gigalitre 10 ^9 L
//    'TL'         => 0,            // teralitre 10 ^12 L
//    'PL'         => 0,            // petalitre 10 ^15 L
//    'EL'         => 0,            // exalitre 10 ^18 L
//    'ZL'         => 0,            // zettalitre 10 ^21 L
//    'YL'         => 0,            // yottalitre 10 ^24 L
    'cubic_inch' => '0.016387064',  //
    'cubic_foot' => '28.316846592', // = XXX cubic inches?
    'us_gallon'  => '3.785411784',  // U.S liquid gallon
    'uk_gallon'  => '4.54609',      // imperial (uk) gallon
    'pint'       => '0.56826125',   // imperial pint
    'us_pint'    => '0.473176473',  // U.S. fluid pint
    );

    protected static $lookup = array(
    'm³'          => 'kl',
    'kilolitre'   => 'kl',        'kilolitres'    => 'kl',
    'cubic meter' => 'kl',        'cubic meters'  => 'kl',
    'cubic metre' => 'kl',        'cubic metres'  => 'kl',
    'milliliter'  => 'ml',        'milliliters'   => 'ml',
    'millilitre'  => 'ml',        'millilitres'   => 'ml',
    'centiliter'  => 'cl',        'centiliters'   => 'cl',
    'centilitre'  => 'cl',        'centilitres'   => 'cl',
    'deciliter'   => 'dl',        'deciliters'    => 'dl',
    'decilitre'   => 'dl',        'decilitres'    => 'dl',
    'liter'       => 'l',         'liters'        => 'l',
    'litre'       => 'l',         'litres'        => 'l',
    'gallon'      => 'us_gallon', 'gallons'       => 'us_gallon',
    'us gallon'   => 'us_gallon', 'us gallons'    => 'us_gallon',
    'uk gallon'   => 'uk_gallon', 'uk gallons'    => 'uk_gallon',
    );

    /**
     * @param $name unit name or shortcode
     * @return shortcode for the unit name
     */
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
