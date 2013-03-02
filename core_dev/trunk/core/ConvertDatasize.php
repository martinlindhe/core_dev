<?php
/**
 * $Id$
 *
 * Conversion functions between different byte representations
 *
 * http://en.wikipedia.org/wiki/Units_of_information
 * http://en.wikipedia.org/wiki/Template:Quantities_of_bits
 * http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IConvert.php');

class ConvertDatasize implements IConvert
{
    protected static $scale = array( ///< unit scale to a bit
    'bit'  => '1',
    'kbit' => '1024',                      // 2^10
    'mbit' => '1048576',                   // 2^20
    'gbit' => '1073741824',                // 2^30
    'tbit' => '1099511627776',             // 2^40
    'pbit' => '1125899906842624',          // 2^50
    'ebit' => '1152921504606846976',       // 2^60
    'zbit' => '1180591620717411303424',    // 2^70
    'ybit' => '1208925819614629174706176', // 2^80
    'byte' => '8',
    'kb'   => '8192',                      // (2^10)*8
    'mb'   => '8388608',                   // (2^20)*8
    'gb'   => '8589934592',                // (2^30)*8
    'tb'   => '8796093022208',             // (2^40)*8
    'pb'   => '9007199254740992',          // (2^50)*8
    'eb'   => '9223372036854775808',       // (2^60)*8
    'zb'   => '9444732965739290427392',    // (2^70)*8
    'yb'   => '9671406556917033397649408', // (2^80)*8
    );

    protected static $lookup = array(
    'bit'       => 'bit',  'bits'      => 'bit',
    'kilobit'   => 'kbit', 'kilobits'  => 'kbit',
    'megabit'   => 'mbit', 'megabits'  => 'mbit',
    'gigabit'   => 'gbit', 'gigabits'  => 'gbit',
    'terabit'   => 'tbit', 'terabits'  => 'tbit',
    'petabit'   => 'pbit', 'petabits'  => 'pbit',
    'exabit'    => 'ebit', 'exabits'   => 'ebit',
    'zettabit'  => 'zbit', 'zettabits' => 'zbit',
    'yottabit'  => 'ybit', 'yottabits' => 'ybit',
    'b'         => 'byte', 'bytes' => 'byte',
    'kilobyte'  => 'kb',   'kbyte' => 'kb', 'kib' => 'kb', 'k' => 'kb',
    'megabyte'  => 'mb',   'mbyte' => 'mb', 'mib' => 'mb', 'm' => 'mb',
    'gigabyte'  => 'gb',   'gbyte' => 'gb', 'gib' => 'gb', 'g' => 'gb',
    'terabyte'  => 'tb',   'tbyte' => 'tb', 'tib' => 'tb', 't' => 'tb',
    'petabyte'  => 'pb',
    'exabyte'   => 'eb',
    'zettabyte' => 'zb',
    'yottabyte' => 'yb',
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
